{{-- resources/views/admin/users.blade.php --}}
<x-layout>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Users / Staff</h2>
        <small class="text-muted">Admins manage roles and access</small>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <form id="filterForm" method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input id="searchInput" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, email or phone">
        </div>

        <div class="col-md-3">
            <select id="roleFilter" name="role" class="form-select">
                <option value="">All roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r }}" {{ request('role')===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Address</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->phone }}</td>
                        <td>{{ $u->email ?? '—' }}</td>
                        <td class="text-capitalize">{{ $u->role }}</td>
                        <td>{{ Str::limit($u->address,30) }}</td>
                        <td>{{ $u->created_at->format('Y-m-d') }}</td>
                        <td class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#showUserModal" data-id="{{ $u->id }}">View</button>

                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="{{ $u->id }}">Edit</button>

                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-action="{{ route('admin.users.destroy', $u) }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>Showing {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</div>
        <div>{{ $users->links() }}</div>
    </div>
</div>

{{-- Show Modal --}}
<div class="modal fade" id="showUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" id="showUserModalContent">
            <div class="modal-header">
                <h5 class="modal-title">Loading...</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" id="editUserModalContent">
            <div class="modal-header">
                <h5 class="modal-title">Loading...</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
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
            <div class="modal-body">Are you sure you want to delete this user? This cannot be undone.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto submit search/role filter (500ms)
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    let timeout = null;
    const submitLater = () => { if(timeout) clearTimeout(timeout); timeout = setTimeout(()=> filterForm.submit(), 500); };
    if (searchInput) searchInput.addEventListener('input', submitLater);
    if (roleFilter) roleFilter.addEventListener('change', submitLater);

    // dynamic delete modal
    const deleteModal = document.getElementById('confirmDeleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const action = btn.getAttribute('data-action');
        document.getElementById('deleteForm').setAttribute('action', action);
    });

    // load show modal via AJAX
    const showModal = document.getElementById('showUserModal');
    showModal.addEventListener('show.bs.modal', async function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const target = document.getElementById('showUserModalContent');
        target.innerHTML = `<div class="modal-header"><h5 class="modal-title">Loading...</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center"><div class="spinner-border" role="status"></div></div>`;
        try {
            const resp = await fetch(`/admin/users/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            if (!resp.ok) throw new Error('Fetch failed');
            const html = await resp.text();
            target.innerHTML = html;
        } catch (err) {
            target.innerHTML = `<div class="modal-header"><h5 class="modal-title">Error</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-danger">Could not load user details.</div>`;
            console.error(err);
        }
    });

    // load edit modal via AJAX (fetch /admin/users/{id}/edit)
    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', async function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const target = document.getElementById('editUserModalContent');
        target.innerHTML = `<div class="modal-header"><h5 class="modal-title">Loading...</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center"><div class="spinner-border" role="status"></div></div>`;
        try {
            const resp = await fetch(`/admin/users/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            if (!resp.ok) throw new Error('Fetch failed');
            const html = await resp.text();
            target.innerHTML = html;
        } catch (err) {
            target.innerHTML = `<div class="modal-header"><h5 class="modal-title">Error</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-danger">Could not load edit form.</div>`;
            console.error(err);
        }
    });
});
</script>
</x-layout>
