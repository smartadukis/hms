{{-- resources/views/admin/users/show.blade.php --}}
<div class="modal-header">
    <h5 class="modal-title">User Details — {{ $user->name }}</h5>
    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Phone:</strong> {{ $user->phone }}</p>
    <p><strong>Email:</strong> {{ $user->email ?? '—' }}</p>
    <p><strong>Role:</strong> <span class="text-capitalize">{{ $user->role }}</span></p>
    <p><strong>Address:</strong> {{ $user->address ?? '—' }}</p>
    <p><strong>Joined:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
