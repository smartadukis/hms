<x-layout>
    <div class="container">
        <h2 class="text-info mb-4">Patients List</h2>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">

            <a href="{{ route('patients.create') }}" class="btn btn-md btn-info px-4 mb-2 mb-md-0 me-5" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                + New Patient
            </a>

            <form id="filterForm" method="GET" class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <input type="text" name="search" id="searchInput" placeholder="Search..." class="form-control" value="{{ request('search') }}">

                <select name="gender" id="genderSelect" class="form-select">
                    <option value="">All Genders</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>

                @php $role = auth()->user()->role; @endphp

                @if($role === 'admin')
                <select name="creator" id="creatorSelect" class="form-select">
                    <option value="">All Creators</option>
                    @foreach($creators as $creator)
                        <option value="{{ $creator->id }}" {{ (string)request('creator') === (string)$creator->id ? 'selected' : '' }}>
                            {{ $creator->name }}
                        </option>
                    @endforeach
                </select>
                @endif

                <button type="submit" class="btn btn-info">Filter</button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->phone }}</td>
                        <td>{{ $patient->gender }}</td>
                        <td>{{ \Carbon\Carbon::parse($patient->dob)->format('d M, Y') }}</td>
                        <td>{{ $patient->creator->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-action="{{ route('patients.destroy', $patient) }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No patients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $patients->links() }}
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
                        Are you sure you want to delete this patient? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter + Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const genderSelect = document.getElementById('genderSelect');
            const creatorSelect = document.getElementById('creatorSelect');
            const deleteForm = document.getElementById('deleteForm');
            const confirmModal = document.getElementById('confirmDeleteModal');

            if (genderSelect) {
                genderSelect.addEventListener('change', () => filterForm.submit());
            }

            if (creatorSelect) {
                creatorSelect.addEventListener('change', () => filterForm.submit());
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(() => filterForm.submit(), 500);
            });

            searchInput.addEventListener('change', () => {
                clearTimeout(window.searchTimeout);
                filterForm.submit();
            });

            confirmModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                deleteForm.setAttribute('action', action);
            });
        });
    </script>
</x-layout>
