<x-layout>
    <div class="container">
        <h2 class="mb-4">Edit Prescription</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Oops!</strong> Please fix the following errors:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('prescriptions.update', $prescription) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Patient</label>
                    <select name="patient_id" class="form-select">
                        <option value="">-- Select Patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $prescription->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="notes" class="form-label">General Notes (Optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="E.g. Follow up in 1 week"
                        value="{{ old('notes', $prescription->notes) }}">
                </div>
            </div>

            <hr>
            <h5>Medications</h5>

            <div id="medication-rows">
                
           @php
            $oldMeds = old('medications', $prescription->items->map(function($item) {
                return [
                    'medication_id' => $item->medication_id,
                    'dosage_quantity' => $item->dosage_quantity,
                    'dosage_unit' => $item->dosage_unit,
                    'frequency' => $item->frequency,
                    'duration' => $item->duration,
                    'instructions' => $item->instructions,
                ];
            })->toArray());
            @endphp


                @foreach ($oldMeds as $i => $med)
                    @include('prescriptions._medication-row', ['i' => $i, 'med' => $med, 'medications' => $medications])
                @endforeach
            </div>

            <button type="button" class="btn btn-sm btn-secondary mb-3" id="add-medication-row">+ Add Medication</button>

            <div class="d-flex justify-content-between">
                <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Prescription</button>
            </div>
        </form>
    </div>

    {{-- JavaScript for dynamic rows --}}
    <template id="medication-template">
        @include('prescriptions._medication-row', ['i' => '__INDEX__', 'med' => [], 'medications' => $medications])
    </template>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addBtn = document.getElementById('add-medication-row');
            const container = document.getElementById('medication-rows');
            const template = document.getElementById('medication-template').innerHTML;
            let index = {{ count($oldMeds) }};

            addBtn.addEventListener('click', () => {
                const newRow = template.replace(/__INDEX__/g, index);
                container.insertAdjacentHTML('beforeend', newRow);
                index++;
            });
        });
    </script>
</x-layout>
