<div class="medication-group border p-3 rounded mb-3 bg-light">
    <div class="row">
        <div class="col-md-6">
            <label>Medication</label>
            <select name="medications[{{ $i }}][medication_id]" class="form-select">
                <option value="">-- Choose Medication --</option>
                @foreach($medications as $med)
                    <option value="{{ $med->id }}"
                        {{ isset($med['medication_id']) && $med['medication_id'] == $med->id ? 'selected' : '' }}>
                        {{ $med->name }} ({{ $med->strength }} {{ $med->unit_of_strength }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Qty</label>
            <input type="number" name="medications[{{ $i }}][dosage_quantity]" class="form-control"
                   value="{{ $med['dosage_quantity'] ?? '' }}" placeholder="e.g. 2">
        </div>
        <div class="col-md-2">
            <label>Unit</label>
            <input type="text" name="medications[{{ $i }}][dosage_unit]" class="form-control"
                   value="{{ $med['dosage_unit'] ?? '' }}" placeholder="e.g. tablet">
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-danger btn-sm d-block w-100" onclick="removeRow(this)">Remove</button>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label>Frequency</label>
            <input type="text" name="medications[{{ $i }}][frequency]" class="form-control"
                   value="{{ $med['frequency'] ?? '' }}" placeholder="e.g. twice daily">
        </div>
        <div class="col-md-4">
            <label>Duration</label>
            <input type="text" name="medications[{{ $i }}][duration]" class="form-control"
                   value="{{ $med['duration'] ?? '' }}" placeholder="e.g. 5 days">
        </div>
        <div class="col-md-4">
            <label>Instructions (optional)</label>
            <input type="text" name="medications[{{ $i }}][instructions]" class="form-control"
                   value="{{ $med['instructions'] ?? '' }}" placeholder="e.g. after meals">
        </div>
    </div>
</div>
