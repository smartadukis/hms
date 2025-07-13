<x-layout>
    <div class="container">
        <h2 class="mb-4">Medications</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('medications.index') }}" id="filterForm" class="row mb-3">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, generic name or barcode" class="form-control" id="searchInput">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" id="categoryFilter">
                    <option value="">-- Filter by Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('medications.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <a href="{{ route('medications.create') }}" class="btn btn-success mb-3">+ Add Medication</a>

        <table class="table table-bordered table-striped" id="medicationTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Strength</th>
                    <th>Category</th>
                    <th>Pack Size</th>
                    <th>Manufacturer</th>
                    <th>Controlled?</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCount = 0;
                    $totalPackSize = 0;
                @endphp

                @forelse ($medications as $med)
                    @php
                        $totalCount++;
                        $totalPackSize += $med->pack_size;
                    @endphp

                    <tr>
                        <td>{{ $med->name }}</td>
                        <td>{{ $med->strength }} {{ $med->unit_of_strength }}</td>
                        <td>{{ $med->category }}</td>
                        <td>{{ $med->pack_size }} {{ $med->dispensing_unit }}</td>
                        <td>{{ $med->manufacturer ?? 'N/A' }}</td>
                        <td>{{ $med->is_controlled ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('medications.edit', $med) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-action="{{ route('medications.destroy', $med) }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No medications found.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="table-dark text-white">
                <tr>
                    <td>Total Medications: {{ $totalCount }}</td>
                    <td colspan="2"></td>
                    <td>Total Pack Sizes: {{ $totalPackSize }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        {{ $medications->links() }}
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
                        Are you sure you want to delete this medication? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterForm = document.getElementById('filterForm');
            const filterSelect = document.getElementById('categoryFilter');
            const searchInput = document.getElementById('searchInput');
            const deleteForm = document.getElementById('deleteForm');
            const deleteModal = document.getElementById('confirmDeleteModal');

            let timeout = null;

            const handleFilter = () => {
                if (timeout) clearTimeout(timeout);
                timeout = setTimeout(() => filterForm.submit(), 500);
            };

            filterSelect.addEventListener('change', handleFilter);
            searchInput.addEventListener('input', handleFilter);

            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                deleteForm.setAttribute('action', action);
            });
        });
    </script>
</x-layout>
