<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{

    public function index()
    {
        $patients = Patient::with('creator')->latest()->paginate(25);
        return view('patients.index', compact('patients'));
    }


    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:patients',
            'email' => 'nullable|email',
            'gender' => 'required|in:Male,Female',
            'dob' => 'required|date',
            'blood_group' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        Patient::create([
            ...$request->all(),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient created.');
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:patients,phone,' . $patient->id,
            'email' => 'nullable|email',
            'gender' => 'required|in:Male,Female',
            'dob' => 'required|date',
            'blood_group' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient updated.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return back()->with('success', 'Patient deleted.');
    }
}

