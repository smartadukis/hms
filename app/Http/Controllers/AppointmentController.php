<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AppointmentController
 *
 * Handles appointment CRUD operations for admins and doctors,
 * including listing, creation, updates, deletions, and
 * doctor-specific appointment management.
 */
class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments with optional status filter.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest scheduled date first
        $appointments = $query->orderBy('scheduled_at', 'desc')->paginate(25);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show form for creating a new appointment.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch patients and doctors for selection
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();

        return view('appointments.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created appointment.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'notes' => 'nullable|string',
        ]);

        // Create appointment with default status 'pending'
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
     * Display the specified appointment.
     *
     * @param string $id
     */
    public function show(string $id)
    {
        // Currently unused
    }

    /**
     * Show form for editing an existing appointment.
     *
     * @param Appointment $appointment
     * @return \Illuminate\View\View
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Update an existing appointment.
     *
     * @param Request $request
     * @param Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
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
     * Delete an appointment.
     *
     * @param Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }

    /**
     * Display appointments for the logged-in doctor.
     *
     * @return \Illuminate\View\View
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
     * Show edit form for a doctor's appointment.
     *
     * @param Appointment $appointment
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function editDoctor(Appointment $appointment)
    {
        // Prevent doctors from editing other doctors' appointments
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $patients = Patient::orderBy('name')->get();
        return view('appointments.doctor-edit', compact('appointment', 'patients'));
    }

    /**
     * Update a doctor's appointment.
     *
     * @param Request $request
     * @param Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
