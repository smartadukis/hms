<div class="mb-3">
    <label class="form-label">Date</label>
    <input type="date" name="date" class="form-control" value="{{ old('date', $transaction->date ?? now()->toDateString()) }}">
</div>

<div class="mb-3">
    <label class="form-label">Type</label>
    <select name="type" class="form-select">
        <option value="income" {{ old('type', $transaction->type ?? '') === 'income' ? 'selected' : '' }}>Income</option>
        <option value="expense" {{ old('type', $transaction->type ?? '') === 'expense' ? 'selected' : '' }}>Expense</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Account</label>
    <select name="account_id" class="form-select">
        <option value="">-- Select account --</option>
        @foreach($accounts as $acct)
            <option value="{{ $acct->id }}" {{ (string)old('account_id', $transaction->account_id ?? '') === (string)$acct->id ? 'selected' : '' }}>
                {{ $acct->code }} — {{ $acct->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Amount (CAD)</label>
        <input type="number" step="0.01" name="amount" class="form-control" placeholder="e.g. 120.00" value="{{ old('amount', $transaction->amount ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Invoice No. (optional)</label>
        <input type="text" name="invoice_id" class="form-control" placeholder="Enter invoice id if applies" value="{{ old('invoice_id', $transaction->invoice_id ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Description (optional)</label>
    <textarea name="description" class="form-control" placeholder="E.g. Payment for invoice #123">{{ old('description', $transaction->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Receipt (PDF/Image) - optional</label>
    <input type="file" name="receipt" class="form-control">
    @if(!empty($transaction->receipt_path))
        <small>Existing: <a href="{{ Storage::url($transaction->receipt_path) }}" target="_blank">View receipt</a></small>
    @endif
</div>
