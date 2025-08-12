{{-- resources/views/admin/users/edit.blade.php --}}
@php /** @var \App\Models\User $user */ @endphp

<div class="modal-header">
    <h5 class="modal-title">Edit User — {{ $user->name }}</h5>
    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('admin.users.update', $user) }}" method="POST" class="ajax-edit-form">
    @csrf
    @method('PUT')

    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                @foreach($roles as $r)
                    <option value="{{ $r }}" {{ $user->role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>

        {{-- If you add is_active column to users, uncomment this:
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ $user->is_active ? 'checked' : '' }}>
            <label for="isActive" class="form-check-label">Active</label>
        </div>
        --}}

        <p class="small text-muted">Changing a user's role updates their access permissions. You cannot change your own admin role here.</p>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
