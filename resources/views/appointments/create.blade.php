<x-layout>
    <div class="container mt-4">
        <h2>Schedule New Appointment</h2>

        <form action="{{ route('appointments.store') }}" method="POST" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="patient_id" class="form-label">Select Patient</label>
                <select name="patient_id" id="patient_id" class="form-select">
                    <option value="">-- Choose --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>
                @error('patient_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="doctor_id" class="form-label">Assign to Doctor</label>
                <select name="doctor_id" id="doctor_id" class="form-select">
                    <option value="">-- Choose --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
                @error('doctor_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>


            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="scheduled_at" class="form-label">Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control">
                    @error('scheduled_at') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-info">Create Appointment</button>
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-layout>
