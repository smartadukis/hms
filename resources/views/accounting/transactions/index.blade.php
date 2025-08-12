<x-layout>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Transactions</h2>
        <div>
            {{-- Print report button uses current filters --}}
            <button id="printReportBtn" class="btn btn-outline-primary me-2">Print PDF (selected range)</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransactionModal" type="button">+ New Transaction</button>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    {{-- Filters --}}
    <form id="filterForm" method="GET" action="{{ route('transactions.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search description or amount">
        </div>

        <div class="col-md-2">
            <select name="type" id="typeFilter" class="form-select">
                <option value="">All Types</option>
                <option value="income" {{ request('type')==='income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type')==='expense' ? 'selected' : '' }}>Expense</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="account_id" id="accountFilter" class="form-select">
                <option value="">All Accounts</option>
                @foreach($accounts as $acct)
                    <option value="{{ $acct->id }}" {{ (string)request('account_id') === (string)$acct->id ? 'selected' : '' }}>
                        {{ $acct->code }} — {{ $acct->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Account</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Invoice</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr>
                        <td>{{ optional($t->date)->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $t->type==='income' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($t->type) }}
                            </span>
                        </td>
                        <td>{{ $t->account->name ?? '-' }}</td>
                        <td>${{ number_format($t->amount,2) }}</td>
                        <td>{{ Str::limit($t->description, 50) }}</td>
                        <td>{{ $t->invoice ? '#'.$t->invoice->id : '-' }}</td>
                        <td>{{ $t->creator->name ?? '-' }}</td>
                        <td class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal" data-id="{{ $t->id }}">View</button>

                            {{-- Edit fetches the edit form (not the show view) --}}
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $t->id }}">Edit</button>

                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-action="{{ route('transactions.destroy',$t) }}">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $transactions->links() }}
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">New Transaction</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                @include('accounting.transactions._form', ['transaction' => null, 'accounts' => $accounts])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal (contents loaded by JS) --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="editModalContent">
            {{-- dynamically replaced --}}
            <div class="modal-header">
                <h5 class="modal-title">Loading...</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        </div>
    </div>
</div>

{{-- View Modal (contents loaded by JS) --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" id="viewModalContent">
            {{-- dynamically replaced --}}
            <div class="modal-header">
                <h5 class="modal-title">Loading...</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteForm" class="modal-content">
            @csrf @method('DELETE')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this transaction?</div>
            <div class="modal-footer">
                
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // auto-submit filters (500ms)
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const accountFilter = document.getElementById('accountFilter');
    let timeout = null;
    const handler = () => { if (timeout) clearTimeout(timeout); timeout = setTimeout(()=> filterForm.submit(), 500); };
    if (searchInput) searchInput.addEventListener('input', handler);
    if (typeFilter) typeFilter.addEventListener('change', handler);
    if (accountFilter) accountFilter.addEventListener('change', handler);
    filterForm.querySelectorAll('input[type="date"]').forEach(i=>i.addEventListener('change', handler));

    // dynamic delete modal action
    const deleteModal = document.getElementById('confirmDeleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const action = btn.getAttribute('data-action');
        document.getElementById('deleteForm').setAttribute('action', action);
    });

    // load edit modal content via fetch -> use the edit route
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', async function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const target = document.getElementById('editModalContent');

        // show spinner while loading
        target.innerHTML = `
            <div class="modal-header">
                <h5 class="modal-title">Loading...</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `;

        try {
            const resp = await fetch(`/transactions/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) throw new Error('Fetch failed: ' + resp.status);
            const html = await resp.text();
            // inject returned edit-form HTML directly
            target.innerHTML = html;
        } catch (err) {
            console.error(err);
            target.innerHTML = `<div class="modal-header"><h5 class="modal-title">Error</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body text-danger">Could not load edit form. Check console for details.</div>`;
        }
    });

    // load view modal content
    const viewModal = document.getElementById('viewModal');
    viewModal.addEventListener('show.bs.modal', async function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const target = document.getElementById('viewModalContent');

        target.innerHTML = `
            <div class="modal-header">
                <h5 class="modal-title">Loading...</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `;

        try {
            const resp = await fetch(`/transactions/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) throw new Error('Fetch failed: ' + resp.status);
            const html = await resp.text();
            target.innerHTML = html;
        } catch (err) {
            console.error(err);
            target.innerHTML = `<div class="modal-header"><h5 class="modal-title">Error</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body text-danger">Could not load details. Check console for details.</div>`;
        }
    });

    // Print report button: open report route with current filters (use route() to avoid 404s)
    const printBtn = document.getElementById('printReportBtn');
    const reportBase = "{{ route('transactions.report') }}";
    printBtn.addEventListener('click', function () {
        const params = new URLSearchParams(new FormData(filterForm)).toString();
        const url = reportBase + (params ? ('?' + params) : '');
        window.open(url, '_blank');
    });
});
</script>
</x-layout>
