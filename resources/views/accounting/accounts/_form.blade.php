<div class="mb-3">
    <label class="form-label">Account Name</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $account->name ?? '') }}"
           placeholder="e.g. Cash in Hand, Accounts Receivable">
</div>

<div class="mb-3">
    <label class="form-label">Account Code</label>
    <input type="text" name="code" class="form-control"
           value="{{ old('code', $account->code ?? '') }}"
           placeholder="Numeric code, e.g. 1001">
</div>

<div class="mb-3">
    <label class="form-label">Account Type</label>
    <select name="type" class="form-select">
        @foreach(['Asset','Liability','Income','Expense','Equity'] as $t)
            <option value="{{ $t }}" {{ old('type', $account->type ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" {{ (string) old('is_active', isset($account) ? (int)$account->is_active : 1) === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ (string) old('is_active', isset($account) ? (int)$account->is_active : 1) === '0' ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Description (optional)</label>
    <textarea name="description" class="form-control" rows="3" placeholder="Add details or notes about this account">{{ old('description', $account->description ?? '') }}</textarea>
</div>
