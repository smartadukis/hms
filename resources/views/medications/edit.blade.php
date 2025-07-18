<x-layout>
    <div class="container">
        <h2 class="mb-4">Edit Medication</h2>

        <form method="POST" action="{{ route('medications.update', $medication) }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $medication->name) }}" class="form-control" placeholder="e.g. Amoxicillin">
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label>Generic Name (Optional)</label>
                    <input type="text" name="generic_name" value="{{ old('generic_name', $medication->generic_name) }}" class="form-control" placeholder="e.g. Amoxicillin">
                </div>
                <div class="col-md-4">
                    <label>Barcode / NDC (Optional)</label>
                    <input type="text" name="barcode_or_ndc" value="{{ old('barcode_or_ndc', $medication->barcode_or_ndc) }}" class="form-control" placeholder="Unique code or NDC">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Strength</label>
                    <input type="number" step="0.01" name="strength" value="{{ old('strength', $medication->strength) }}" class="form-control" placeholder="e.g. 500">
                    @error('strength') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label>Unit of Strength</label>
                    <select name="unit_of_strength" class="form-select">
                        <option value="">-- Select Unit --</option>
                        @foreach($unitStrengths as $unit)
                            <option value="{{ $unit }}" {{ old('unit_of_strength', $medication->unit_of_strength) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit_of_strength') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label>Pack Size</label>
                    <input type="number" name="pack_size" value="{{ old('pack_size', $medication->pack_size) }}" class="form-control" placeholder="e.g. 30">
                    @error('pack_size') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Dispensing Unit</label>
                    <select name="dispensing_unit" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($dispensingUnits as $unit)
                            <option value="{{ $unit }}" {{ old('dispensing_unit', $medication->dispensing_unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('dispensing_unit') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label>Category</label>
                    <select name="category" class="form-select">
                        <option value="">-- Select Category --</option>
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat }}" {{ old('category', $medication->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label>Manufacturer (Optional)</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer', $medication->manufacturer) }}" class="form-control" placeholder="e.g. GSK">
                </div>
            </div>

             <div class="row mb-3">
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="is_controlled" {{ old('is_controlled') ? 'checked' : '' }}>
                        Controlled Medication? (Optional)
                    </label>
                </div>
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="requires_refrigeration" {{ old('requires_refrigeration') ? 'checked' : '' }}>
                        Requires Refrigeration? (Optional)
                    </label>
                </div>
                <div class="col-md-2">
                    <label for="quantity" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" name="quantity" value="{{ old('quantity', $medication->quantity) }}" required>
                    @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-2">
                    <label for="reorder_level" class="form-label">Reorder Level (optional)</label>
                    <input type="number" class="form-control" name="reorder_level" value="{{ old('reorder_level', $medication->reorder_level) }}">
                    @error('reorder_level') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label>Storage Conditions (Optional)</label>
                <input type="text" name="storage_conditions" value="{{ old('storage_conditions', $medication->storage_conditions) }}" class="form-control" placeholder="e.g. Store below 25°C">
            </div>

            <div class="mb-3">
                <label>Description (Optional)</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Short description...">{{ old('description', $medication->description) }}</textarea>
            </div>

            <button class="btn btn-primary">Update Medication</button>
            <a href="{{ route('medications.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-layout>
