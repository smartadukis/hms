<x-layout>
    <div class="container mt-4">
        <h2 class="mb-3">My Appointments</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
            <!-- <a href="{{ route('appointments.create') }}" class="btn btn-info mb-2 mb-md-0">
                + Schedule New Appointment
            </a> -->


        <!-- <form method="GET" id="filterForm" action="{{ route('doctor.appointments') }}"  class="d-flex flex-column flex-sm-row g-3 w-50">
            <div class="col-md-4">
                <select name="status" class="form-select" id="statusFilter">
                    <option value="">-- Filter by Status --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </form> -->
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
                                <a href="{{ route('doctor.appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterSelect = document.getElementById('statusFilter');
        let timeout = null;

        filterSelect.addEventListener('change', function () {
            if (timeout) clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    });
</script>

</x-layout>
