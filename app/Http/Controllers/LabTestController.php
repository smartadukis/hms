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
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all lab tests
        $tests = LabTest::with('patient', 'doctor', 'requestedBy')->latest()->paginate(10);
        return view('lab_tests.index', compact('tests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Show form to create a new lab test
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();
        return view('lab_tests.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'test_type' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

         LabTest::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'test_type' => $request->test_type,
            'notes' => $request->notes,
            'requested_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('lab-tests.index')->with('success', 'Lab test requested.');
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
    public function edit(LabTest $labTest)
    {
        // Show form to edit the lab test
        $patients = Patient::orderBy('name')->get();
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();
        return view('lab_tests.edit', compact('labTest', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LabTest $labTest)
    {
         $request->validate([
            'status' => 'required|in:pending,processing,completed',
            'result' => 'nullable|string',
            'result_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'status' => $request->status,
            'result' => $request->result,
        ];

        if ($request->hasFile('result_file')) {
            $path = $request->file('result_file')->store('lab_results', 'public');
            $data['result_file'] = $path;
        }

        $labTest->update($data);

        // Notify doctor if result is completed
        // if ($labTest->status === 'completed') {
        //     $doctor = $labTest->doctor;
        //     if ($doctor) {
        //         $doctor->notify(new LabResultReady($labTest));
        //     }
        // }

        return redirect()->route('lab-tests.index')->with('success', 'Lab test updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LabTest $labTest)
    {
        // Delete the lab test
        $labTest->delete();
        return back()->with('success', 'Lab test deleted successfully.');
    }

    /**
     * View the lab test result file.
     */
    public function viewFile($id)
    {
        $test = LabTest::findOrFail($id);

        if (!$test->result_file || !Storage::disk('public')->exists($test->result_file)) {
            abort(404, 'File not found');
        }

        $mimeType = Storage::disk('public')->mimeType($test->result_file);
        $extension = pathinfo($test->result_file, PATHINFO_EXTENSION);

        $filename = Str::slug($test->patient->name . '_' . $test->test_type) . '.' . $extension;

        return response()->file(
            storage_path('app/public/' . $test->result_file),
            ['Content-Type' => $mimeType, 'Content-Disposition' => "inline; filename=\"$filename\""]
        );
    }
}
