<x-layout>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Journal Entries</h2>
        <a href="{{ route('journal.create') }}" class="btn btn-primary">+ New Journal Entry</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    {{-- Filters --}}
    <form id="filterForm" method="GET" action="{{ route('journal.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search reference or description">
        </div>

        <div class="col-md-2">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <select id="creatorFilter" name="created_by" class="form-select">
                <option value="">-- Created by --</option>
                @foreach($creators as $id => $name)
                    <option value="{{ $id }}" {{ (string)request('created_by') === (string)$id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1">
            <select id="approvedFilter" name="approved" class="form-select">
                <option value="">Any</option>
                <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>Approved</option>
                <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>Unapproved</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary">Apply</button>
            <a href="{{ route('journal.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Lines</th>
                <th>Approved</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->entry_date }}</td>
                    <td>{{ $entry->reference }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($entry->description,60) }}</td>
                    <td>{{ $entry->creator->name ?? '—' }}</td>
                    <td>{{ $entry->lines()->count() }}</td>
                    <td>
                        @if($entry->approved)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('journal.show', $entry) }}" class="btn btn-sm btn-info">View</a>
                        <!-- <a href="{{ route('journal.edit', $entry) }}" class="btn btn-sm btn-warning">Edit</a> -->

                        {{-- Delete trigger --}}
                        <button
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-action="{{ route('journal.destroy', $entry) }}"
                        >
                            Delete
                        </button>
                    </td>

                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No journal entries found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center">
        <div>Showing {{ $entries->firstItem() ?? 0 }} - {{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }}</div>
        <div>{{ $entries->links() }}</div>
    </div>
</div>

<!-- Delete Confirmation Modal (single dynamic modal) -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this journal entry? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('confirmDeleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');
        const form = document.getElementById('deleteForm');
        form.setAttribute('action', action);
    });

    // existing filter auto-submit JS remains here...
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const creatorFilter = document.getElementById('creatorFilter');
    const approvedFilter = document.getElementById('approvedFilter');
    const fromInput = form.querySelector('input[name="from"]');
    const toInput = form.querySelector('input[name="to"]');

    let timeout = null;
    const autoSubmit = () => {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(()=> form.submit(), 500);
    };

    if (searchInput) searchInput.addEventListener('input', autoSubmit);
    if (creatorFilter) creatorFilter.addEventListener('change', autoSubmit);
    if (approvedFilter) approvedFilter.addEventListener('change', autoSubmit);
    if (fromInput) fromInput.addEventListener('change', autoSubmit);
    if (toInput) toInput.addEventListener('change', autoSubmit);
});
</script>
</x-layout>
