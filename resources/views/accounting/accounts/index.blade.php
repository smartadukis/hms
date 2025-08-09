<x-layout>
<div class="container mt-4">

    <h2 class="mb-4">Chart of Accounts</h2>

    {{-- Search & Filters --}}
    <form id="filterForm" action="{{ route('accounts.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or code">
        </div>

        <div class="col-md-3">
            <select id="typeFilter" name="type" class="form-select">
                <option value="">-- All Types --</option>
                @foreach($types as $t)
                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select id="statusFilter" name="status" class="form-select">
                <option value="">-- All Status --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary">Apply</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Add button --}}
    <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createAccountModal">+ Add New Account</button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width:110px">Code</th>
                <th>Name</th>
                <th style="width:120px">Type</th>
                <th>Description</th>
                <th style="width:100px">Status</th>
                <th style="width:200px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
                <tr>
                    <td class="align-middle">{{ $account->code }}</td>
                    <td class="align-middle">{{ $account->name }}</td>
                    <td class="align-middle">{{ $account->type }}</td>
                    <td class="align-middle">{{ $account->description }}</td>
                    <td class="align-middle">
                        <span class="badge {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $account->id }}">View</button>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $account->id }}">Edit</button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $account->id }}">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No accounts found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center">
        <div>Showing {{ $accounts->firstItem() ?? 0 }} - {{ $accounts->lastItem() ?? 0 }} of {{ $accounts->total() }}</div>
        <div>{{ $accounts->links() }}</div>
    </div>

    {{-- MODALS: render all modals AFTER the table (valid HTML) --}}
    @foreach($accounts as $account)
        {{-- View Modal --}}
        <div class="modal fade" id="viewModal{{ $account->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Account: {{ $account->name }}</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Code:</strong> {{ $account->code }}</p>
                        <p><strong>Type:</strong> {{ $account->type }}</p>
                        <p><strong>Description:</strong> {{ $account->description ?? '—' }}</p>
                        <p><strong>Status:</strong> {{ $account->is_active ? 'Active' : 'Inactive' }}</p>
                        <p><small class="text-muted">Created: {{ $account->created_at->format('d M, Y') }}</small></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Modal (static backdrop to avoid accidental outside-click close) --}}
        <div class="modal fade" id="editModal{{ $account->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <form action="{{ route('accounts.update', $account->id) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Account</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('accounting.accounts._form', ['account' => $account])
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div class="modal fade" id="deleteModal{{ $account->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="modal-content">
                    @csrf @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Account</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong>{{ $account->name }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Create Modal (static backdrop) --}}
    <div class="modal fade" id="createAccountModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <form action="{{ route('accounts.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Account</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('accounting.accounts._form')
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- JS: auto-submit filters (500ms debounce) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const search = document.getElementById('searchInput');
    const type = document.getElementById('typeFilter');
    const status = document.getElementById('statusFilter');
    let timeout = null;

    const auto = () => {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(()=> form.submit(), 500);
    };

    if (search) search.addEventListener('input', auto);
    if (type) type.addEventListener('change', auto);
    if (status) status.addEventListener('change', auto);
});
</script>
</x-layout>
