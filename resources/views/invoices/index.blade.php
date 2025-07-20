<x-layout>
    <div class="container">
        <h2 class="mb-4">Invoices</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('invoices.create') }}" class="btn btn-primary mb-3">+ New Invoice</a>

        <form id="filterForm" method="GET" action="{{ route('invoices.index') }}" class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" id="searchInput"
                       value="{{ request('search') }}"
                       class="form-control" placeholder="Search by patient name">
            </div>
            <div class="col-md-3">
                <select name="status" id="statusFilter" class="form-select">
                    <option value="">-- Filter by Status --</option>
                    <option value="unpaid"   {{ request('status')=='unpaid'?'selected':'' }}>Unpaid</option>
                    <option value="partial"  {{ request('status')=='partial'?'selected':'' }}>Partial</option>
                    <option value="paid"     {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="issued_by" id="issuerFilter" class="form-select">
                    <option value="">-- Filter by Issuer --</option>
                    @foreach($issuers as $user)
                        <option value="{{ $user->id }}" {{ request('issued_by')==$user->id?'selected':'' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex">
                <button class="btn btn-primary me-2">Apply</button>
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Issued By</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ $i->patient->name }}</td>
                        <td>${{ number_format($i->total_amount,2) }}</td>
                        <td>
                            <span class="badge 
                                {{ $i->status=='unpaid'?'bg-warning text-dark':($i->status=='paid'?'bg-success':'bg-info text-dark') }}">
                                {{ ucfirst($i->status) }}
                            </span>
                        </td>
                        <td>{{ $i->issuedBy->name ?? '—' }}</td>
                        <td>{{ $i->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('invoices.show', $i) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('invoices.edit', $i) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-action="{{ route('invoices.destroy', $i) }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $invoices->links() }}
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="deleteForm">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this invoice?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JS: Auto‑filter & Delete Modal --}}
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const form = document.getElementById('filterForm');
        const search = document.getElementById('searchInput');
        const status = document.getElementById('statusFilter');
        const issuer = document.getElementById('issuerFilter');
        const deleteForm = document.getElementById('deleteForm');
        const deleteModal = document.getElementById('deleteModal');
        let timeout;

        const autoSubmit = () => {
            clearTimeout(timeout);
            timeout = setTimeout(()=>form.submit(), 500);
        };

        if (search)   search.addEventListener('input', autoSubmit);
        if (status)   status.addEventListener('change', autoSubmit);
        if (issuer)   issuer.addEventListener('change', autoSubmit);

        deleteModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            deleteForm.action = btn.getAttribute('data-action');
        });
    });
    </script>
</x-layout>
