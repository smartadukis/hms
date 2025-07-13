<x-layout>
    <div class="container mt-4">
        <h2 class="mb-3">Appointments</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
            <a href="{{ route('appointments.create') }}" class="btn btn-info mb-2 mb-md-0">
                + Schedule New Appointment
            </a>

            <form method="GET" id="filterForm" action="{{ route('appointments.index') }}" class="d-flex flex-column flex-sm-row g-3 w-50">
                <div class="col-md-4">
                    <select name="status" class="form-select" id="statusFilter">
                        <option value="">-- Filter by Status --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Scheduled At</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->patient->name }}</td>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M, Y h:i A') }}</td>
                            <td><span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'secondary') }}">{{ ucfirst($appointment->status) }}</span></td>
                            <td>{{ $appointment->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal"
                                        data-action="{{ route('appointments.destroy', $appointment) }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $appointments->links() }}
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this appointment? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Auto-submit filter --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterSelect = document.getElementById('statusFilter');
            const filterForm = document.getElementById('filterForm');
            let timeout = null;

            filterSelect.addEventListener('change', function () {
                if (timeout) clearTimeout(timeout);
                timeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            });

            // Handle delete modal
            const deleteForm = document.getElementById('deleteForm');
            const confirmModal = document.getElementById('confirmDeleteModal');

            confirmModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                deleteForm.setAttribute('action', action);
            });
        });
    </script>
</x-layout>
