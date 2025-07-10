<x-layout>
    <div class="container">
        <h2 class="mb-4">Request Lab Test</h2>

        <form action="{{ route('lab-tests.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Patient</label>
                <select name="patient_id" class="form-select">
                    <option value="">Select Patient</option>
                    @foreach($patients as $patient)
                        <option  value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->name }}</option>
                    @endforeach
                </select>
                @error('patient_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Doctor</label>
                <select name="doctor_id" class="form-select">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                    @endforeach
                </select>
                @error('doctor_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Test Type</label>
                <input type="text" name="test_type" value="{{ old('test_type') }}" class="form-control" placeholder="Enter test type">
                @error('test_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Submit Request</button>
            <a href="{{ route('lab-tests.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-layout>
