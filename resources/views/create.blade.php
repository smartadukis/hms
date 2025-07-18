<x-layout>
    <div class="container">
        <h2 class="mb-4">New Prescription</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>There were some errors:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('prescriptions.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="patient_id">Select Patient</label>
                    <select name="patient_id" class="form-select">
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="notes">General Notes (optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="e.g. Take before breakfast" value="{{ old('notes') }}">
                </div>
            </div>

            <hr>

            <h5>Prescribed Medications</h5>

            <div id="medication-rows">
                @if(old('medications'))
                    @foreach(old('medications') as $i => $med)
                        @include('prescriptions._medication-row', ['i' => $i, 'med' => $med])
                    @endforeach
                @else
                    @include('prescriptions._medication-row', ['i' => 0])
                @endif
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-secondary" onclick="addMedicationRow()">+ Add Another Medication</button>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Prescription</button>
            </div>
        </form>
    </div>

    <script>
        let rowIndex = {{ old('medications') ? count(old('medications')) : 1 }};
        const medications = @json($medications);

        function addMedicationRow() {
            const container = document.getElementById('medication-rows');
            const newRow = document.createElement('div');
            newRow.innerHTML = `
                @include('prescriptions._medication-row-js-template')
            `.replace(/__INDEX__/g, rowIndex);

            container.appendChild(newRow);
            rowIndex++;
        }

        function removeRow(button) {
            button.closest('.medication-group').remove();
        }
    </script>
</x-layout>
