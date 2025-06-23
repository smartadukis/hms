<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Appointment::with(['patient', 'doctor']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('scheduled_at', 'desc')->paginate(25);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();
        return view('appointments.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'notes' => 'nullable|string',
        ]);

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment scheduled successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }

    /**
     * Display appointments for the logged-in doctor.
     */
    public function doctorAppointments()
{
    $appointments = Appointment::where('doctor_id', auth()->id())
        ->with('patient')
        ->orderBy('scheduled_at', 'desc')
        ->paginate(25);

    return view('appointments.doctor-index', compact('appointments'));
}
    /**
     * Show the form for editing an appointment for the doctor.
     */
    public function editDoctor(Appointment $appointment)
    {
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $patients = Patient::orderBy('name')->get();
        return view('appointments.doctor-edit', compact('appointment', 'patients'));
    }
    /**
     * Update an appointment for the doctor.
     */
    public function updateDoctor(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('doctor.appointments')->with('success', 'Appointment updated successfully.');
    }

}
