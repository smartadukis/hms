<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $patient->name ?? '') }}">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone ?? '') }}">
        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Email (optional)</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email ?? '') }}">
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
            <option value="">-- Select --</option>
            <option value="Male" {{ old('gender', $patient->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ old('gender', $patient->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
        </select>
        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Blood Group (Optional) </label>
        <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group', $patient->blood_group ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Date of Birth</label>
    <input type="date" name="dob" class="form-control" value="{{ old('dob', $patient->dob ?? '') }}">
    @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Address (Optional)</label>
    <textarea name="address" class="form-control">{{ old('address', $patient->address ?? '') }}</textarea>
</div>
