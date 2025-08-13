<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles patient management including listing, creating, updating, and deleting patients.
 */
class PatientController extends Controller
{
    /**
     * Display a paginated list of patients with optional filters (search, gender, creator).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        // Filter by gender if provided
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by creator (staff who created the patient record)
        if ($request->filled('creator')) {
            $query->where('created_by', $request->creator);
        }

        // Load patients with creator relationship
        $patients = $query->with('creator')
                          ->latest()
                          ->paginate(25)
                          ->withQueryString();

        $creators = User::select('id', 'name')->get();

        return view('patients.index', compact('patients', 'creators'));
    }

    /**
     * Show the patient creation form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'phone'       => 'required|unique:patients',
            'email'       => 'nullable|email',
            'gender'      => 'required|in:Male,Female',
            'dob'         => 'required|date',
            'blood_group' => 'nullable|string',
            'address'     => 'nullable|string',
        ]);

        // Save patient with the ID of the authenticated user
        Patient::create([
            ...$request->all(),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient created.');
    }

    /**
     * Show the edit form for the specified patient.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\View\View
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient's details in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name'        => 'required',
            'phone'       => 'required|unique:patients,phone,' . $patient->id,
            'email'       => 'nullable|email',
            'gender'      => 'required|in:Male,Female',
            'dob'         => 'required|date',
            'blood_group' => 'nullable|string',
            'address'     => 'nullable|string',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient updated.');
    }

    /**
     * Remove the specified patient from the database.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return back()->with('success', 'Patient deleted.');
    }
}
