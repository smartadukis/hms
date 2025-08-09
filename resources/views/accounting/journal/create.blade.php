<x-layout>
<div class="container mt-4">
    <h2>Create Journal Entry</h2>

    @if ($errors->any())
      <div class="alert alert-danger"><ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('journal.store') }}" method="POST" id="journalForm">
        @csrf

        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', now()->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Reference (optional)</label>
                <input type="text" name="reference" class="form-control" placeholder="e.g. JV/2025/001" value="{{ old('reference') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Description (optional)</label>
                <input type="text" name="description" class="form-control" placeholder="Narration..." value="{{ old('description') }}">
            </div>
        </div>

        <h5>Lines</h5>
        <div id="linesContainer">
            {{-- initial two lines --}}
            @php $oldLines = old('lines', [['account_id'=>'','debit'=>'','credit'=>'','narration'=>''], ['account_id'=>'','debit'=>'','credit'=>'','narration'=>'']]) @endphp
            @foreach($oldLines as $i => $ln)
                <div class="row mb-2 line-row">
                    <div class="col-md-5">
                        <select name="lines[{{ $i }}][account_id]" class="form-select">
                            <option value="">-- choose account --</option>
                            @foreach($accounts as $acct)
                                <option value="{{ $acct->id }}" {{ (string)($ln['account_id'] ?? '') === (string)$acct->id ? 'selected' : '' }}>
                                    {{ $acct->code }} — {{ $acct->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="lines[{{ $i }}][debit]" class="form-control debit" placeholder="Debit" value="{{ $ln['debit'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="lines[{{ $i }}][credit]" class="form-control credit" placeholder="Credit" value="{{ $ln['credit'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="lines[{{ $i }}][narration]" class="form-control" placeholder="Narration" value="{{ $ln['narration'] ?? '' }}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-line">×</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-sm btn-secondary" id="addLine">+ Add Line</button>
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
                <strong>Totals:</strong>
                <span id="totalDebit">0.00</span> / <span id="totalCredit">0.00</span>
            </div>
            <div>
                <a href="{{ route('journal.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Journal</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('linesContainer');
    const addLineBtn = document.getElementById('addLine');

    const updateTotals = () => {
        let totalD = 0, totalC = 0;
        container.querySelectorAll('.line-row').forEach(row => {
            const d = parseFloat(row.querySelector('.debit').value || 0);
            const c = parseFloat(row.querySelector('.credit').value || 0);
            totalD += d; totalC += c;
        });
        document.getElementById('totalDebit').textContent = totalD.toFixed(2);
        document.getElementById('totalCredit').textContent = totalC.toFixed(2);
    };

    // remove line
    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-line')) {
            const row = e.target.closest('.line-row');
            row.remove();
            updateTotals();
        }
    });

    // add line
    addLineBtn.addEventListener('click', function () {
        const idx = container.querySelectorAll('.line-row').length;
        const template = `
            <div class="row mb-2 line-row">
                <div class="col-md-5">
                    <select name="lines[${idx}][account_id]" class="form-select">
                        <option value="">-- choose account --</option>
                        @foreach($accounts as $acct)
                            <option value="{{ $acct->id }}">{{ $acct->code }} — {{ $acct->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="lines[${idx}][debit]" class="form-control debit" placeholder="Debit">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="lines[${idx}][credit]" class="form-control credit" placeholder="Credit">
                </div>
                <div class="col-md-2">
                    <input type="text" name="lines[${idx}][narration]" class="form-control" placeholder="Narration">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-line">×</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
    });

    // live totals update
    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('debit') || e.target.classList.contains('credit')) {
            updateTotals();
        }
    });

    // prevent submit if not balanced
    document.getElementById('journalForm').addEventListener('submit', function (ev) {
        const td = parseFloat(document.getElementById('totalDebit').textContent || 0);
        const tc = parseFloat(document.getElementById('totalCredit').textContent || 0);
        if (td.toFixed(2) !== tc.toFixed(2)) {
            alert('Journal entry must be balanced. Total debits must equal total credits.');
            ev.preventDefault();
            return false;
        }
    });

    // initial totals
    updateTotals();
});
</script>
</x-layout>
