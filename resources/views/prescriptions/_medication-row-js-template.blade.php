<div class="medication-group border p-3 rounded mb-3 bg-light">
    <div class="row">
        <div class="col-md-6">
            <label>Medication</label>
            <select name="medications[__INDEX__][medication_id]" class="form-select">
                <option value="">-- Choose Medication --</option>
                @foreach($medications as $med)
                    <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->strength }} {{ $med->unit_of_strength }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Qty</label>
            <input type="number" name="medications[__INDEX__][dosage_quantity]" class="form-control" placeholder="e.g. 1">
        </div>
        <div class="col-md-2">
            <label>Unit</label>
            <input type="text" name="medications[__INDEX__][dosage_unit]" class="form-control" placeholder="e.g. ml">
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-danger btn-sm d-block w-100" onclick="removeRow(this)">Remove</button>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label>Frequency</label>
            <input type="text" name="medications[__INDEX__][frequency]" class="form-control" placeholder="e.g. 3 times daily">
        </div>
        <div class="col-md-4">
            <label>Duration</label>
            <input type="text" name="medications[__INDEX__][duration]" class="form-control" placeholder="e.g. 7 days">
        </div>
        <div class="col-md-4">
            <label>Instructions (optional)</label>
            <input type="text" name="medications[__INDEX__][instructions]" class="form-control" placeholder="if any">
        </div>
    </div>
</div>
