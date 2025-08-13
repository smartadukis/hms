<?php

// app/Http/Controllers/PrescriptionController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\User;
use App\Models\Medication;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the prescriptions.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Prescription::with('patient', 'doctor', 'items.medication');

        // Doctors see only their own prescriptions
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $prescriptions = $query->latest()->paginate(10);

        $doctors = User::where('role', 'doctor')->get(); // For the filter dropdown

        return view('prescriptions.index', compact('prescriptions', 'doctors'));
    }


    /**
     * Show the form for creating a new prescription.
     * 
     */
    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $medications = Medication::orderBy('name')->get();

        return view('prescriptions.create', compact('patients', 'medications'));
    }

    /**
     * Store a newly created prescription in storage.
     * 
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'nullable|string',
            'medications' => 'required|array|min:1',
            'medications.*.medication_id' => 'required|exists:medications,id',
            'medications.*.dosage_quantity' => 'required|integer|min:1',
            'medications.*.dosage_unit' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.duration' => 'required|string',
            'medications.*.instructions' => 'nullable|string',
        ]);

        $prescription = Prescription::create([
            'doctor_id' => Auth::id(),
            'patient_id' => $request->patient_id,
            'created_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        foreach ($request->medications as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.index')->with('success', 'Prescription added successfully.');
    }

    /**
     * Display the specified prescription.
     */
    public function show(Prescription $prescription)
    {
        $prescription->load('patient', 'doctor', 'items.medication');

        // Authorization: Doctor can only see their prescriptions
        $user = auth()->user();
        if ($user->role === 'doctor' && $prescription->doctor_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form to edit an existing prescription.
     */
    public function edit(Prescription $prescription)
    {
       // $this->authorize('update', $prescription); // Optional if using policies

        $medications = Medication::orderBy('name')->get();
        $patients = Patient::orderBy('name')->get();

        $prescription->load('items');

        return view('prescriptions.edit', compact('prescription', 'medications', 'patients'));
    }

    /**
     * Update an existing prescription in the database.
     */
    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'nullable|string',
            'medications' => 'required|array|min:1',
            'medications.*.medication_id' => 'required|exists:medications,id',
            'medications.*.dosage_quantity' => 'required|integer|min:1',
            'medications.*.dosage_unit' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.duration' => 'required|string',
            'medications.*.instructions' => 'nullable|string',
        ]);

        $prescription->update([
            'patient_id' => $request->patient_id,
            'notes' => $request->notes,
        ]);

        // Remove old items
        $prescription->items()->delete();

        // Add updated items
        foreach ($request->medications as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.index')->with('success', 'Prescription updated successfully.');
    }

    /**
     * Remove the specified prescription from storage.
     * 
     */
    public function destroy(Prescription $prescription)
    {
        //$this->authorize('delete', $prescription); // Optional if using policies

        $prescription->delete();

        return redirect()->route('prescriptions.index')->with('success', 'Prescription deleted successfully.');
    }

/**
 * Show form to change status/notes.
 */
public function editStatus(Prescription $prescription)
{
    //$this->authorize('update', $prescription);

    return view('prescriptions.edit-status', compact('prescription'));
}

/**
 * Update only status and notes, adjust stock if dispensed.
 */
public function updateStatus(Request $request, Prescription $prescription)
{
    $request->validate([
        'status' => 'required|in:pending,partial,dispensed',
        'notes'  => 'nullable|string',
    ]);

    $old = $prescription->status;
    $prescription->update($request->only('status','notes'));

    // If newly marked dispensed, subtract stock
    if ($old !== 'dispensed' && $request->status === 'dispensed') {
        foreach ($prescription->items as $item) {
            $med = $item->medication;
            // subtract quantity, prevent negative
            $med->decrement('pack_size', $item->dosage_quantity);
        }
    }

    return redirect()->route('prescriptions.index')->with('success','Prescription updated.');
}

   /**
     * Dispense a prescription.
     */
    public function dispense(Prescription $prescription)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'pharmacist'])) {
            abort(403, 'Unauthorized');
        }

        // Only dispense once
        if ($prescription->status === 'dispensed') {
            return back()->with('success', 'Already dispensed.');
        }

        foreach ($prescription->items as $item) {
            $med = $item->medication;

            // Check if stock is enough
            if ($med->quantity < $item->dosage_quantity) {
                return back()->with('error', "Insufficient stock for {$med->name}");
            }

            // Deduct from inventory
            $med->decrement('quantity', $item->dosage_quantity);
        }

        $prescription->update(['status' => 'dispensed']);

        return back()->with('success', 'Prescription dispensed and stock updated.');
    }


}


