<x-layout>
    <div class="container">
        <h2 class="mb-4">Prescriptions</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('prescriptions.index') }}" class="row g-3 mb-3" id="filterForm">
            <div class="col-md-5">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="Search by patient name">
            </div>

            @if(auth()->user()->role !== 'doctor')
                <div class="col-md-4">
                    <select name="doctor_id" id="doctorFilter" class="form-select">
                        <option value="">-- Filter by Doctor --</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                                {{ $doc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3">
                <button class="btn btn-primary">Apply</button>
                <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>



        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prescriptions as $prescription)
                    <tr>
                        <td>{{ $prescription->patient->name }}</td>
                        <td>{{ $prescription->doctor->name }}</td>
                        <td>{{ $prescription->created_at->format('Y-m-d') }}</td>
                        <td>
                            <span class="badge 
                                {{ $prescription->status==='pending' ? 'bg-secondary' : ($prescription->status==='partial' ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ ucfirst($prescription->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-sm btn-info">View</a>

                            @php
                                $user = auth()->user();
                                $isDoctorOwner = $user->role === 'doctor' && $prescription->doctor_id === $user->id;
                                $canManage = in_array($user->role, ['admin', 'receptionist']) || $isDoctorOwner;
                            @endphp

                            @if ($canManage)
                                <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-sm btn-primary">Edit</a>

                                <button 
                                    class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-id="{{ $prescription->id }}"
                                >
                                    Delete
                                </button>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr><td colspan="5">No prescriptions found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $prescriptions->links() }}
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this prescription? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JS to Set Action -->
    <script>
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = document.getElementById('deleteForm');
            form.action = `/prescriptions/${id}`;
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const doctorSelect = document.getElementById('doctorFilter');
            const deleteForm = document.getElementById('deleteForm');
            const deleteModal = document.getElementById('deleteModal');

            let timeout = null;

            const handleFilter = () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => form.submit(), 500);
            };

            if (searchInput) {
                searchInput.addEventListener('input', handleFilter);
            }

            if (doctorSelect) {
                doctorSelect.addEventListener('change', handleFilter);
            }

            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                deleteForm.action = `/prescriptions/${id}`;
            });
        });
    </script>

</x-layout>
