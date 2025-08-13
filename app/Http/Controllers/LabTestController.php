<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LabTest;
use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\LabResultReady;
use Illuminate\Support\Facades\Storage;

class LabTestController extends Controller
{
    /**
     * Display a paginated listing of all lab tests with related patient, doctor, and requester data.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all lab tests with related models and order by latest
        $tests = LabTest::with('patient', 'doctor', 'requestedBy')->latest()->paginate(10);

        return view('lab_tests.index', compact('tests'));
    }

    /**
     * Show the form for creating a new lab test request.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch patients and doctors for dropdown selections
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();

        return view('lab_tests.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created lab test in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'test_type' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Create the lab test record
        LabTest::create([
            'patient_id'   => $request->patient_id,
            'doctor_id'    => $request->doctor_id,
            'test_type'    => $request->test_type,
            'notes'        => $request->notes,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ]);

        return redirect()->route('lab-tests.index')->with('success', 'Lab test requested.');
    }

    /**
     * Display the specified lab test.
     *
     * @param string $id
     * @return void
     */
    public function show(string $id)
    {
        // Not implemented yet
    }

    /**
     * Show the form for editing an existing lab test.
     *
     * @param \App\Models\LabTest $labTest
     * @return \Illuminate\View\View
     */
    public function edit(LabTest $labTest)
    {
        // Fetch patients and doctors for dropdown selections
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();

        return view('lab_tests.edit', compact('labTest', 'patients', 'doctors'));
    }

    /**
     * Update the specified lab test in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\LabTest $labTest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LabTest $labTest)
    {
        // Validate incoming request data
        $request->validate([
            'status'       => 'required|in:pending,processing,completed',
            'result'       => 'nullable|string',
            'result_file'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Prepare data for update
        $data = [
            'status' => $request->status,
            'result' => $request->result,
        ];

        // Store uploaded result file if provided
        if ($request->hasFile('result_file')) {
            $path = $request->file('result_file')->store('lab_results', 'public');
            $data['result_file'] = $path;
        }

        $labTest->update($data);

        // Optionally notify doctor when result is completed
        // if ($labTest->status === 'completed') {
        //     $doctor = $labTest->doctor;
        //     if ($doctor) {
        //         $doctor->notify(new LabResultReady($labTest));
        //     }
        // }

        return redirect()->route('lab-tests.index')->with('success', 'Lab test updated.');
    }

    /**
     * Remove the specified lab test from the database.
     *
     * @param \App\Models\LabTest $labTest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LabTest $labTest)
    {
        $labTest->delete();

        return back()->with('success', 'Lab test deleted successfully.');
    }

    /**
     * Display the uploaded result file for the specified lab test.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function viewFile($id)
    {
        $test = LabTest::findOrFail($id);

        // Ensure file exists
        if (!$test->result_file || !Storage::disk('public')->exists($test->result_file)) {
            abort(404, 'File not found');
        }

        $mimeType = Storage::disk('public')->mimeType($test->result_file);
        $extension = pathinfo($test->result_file, PATHINFO_EXTENSION);

        $filename = Str::slug($test->patient->name . '_' . $test->test_type) . '.' . $extension;

        return response()->file(
            storage_path('app/public/' . $test->result_file),
            [
                'Content-Type'        => $mimeType,
                'Content-Disposition' => "inline; filename=\"$filename\""
            ]
        );
    }
}
