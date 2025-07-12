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
                            <form action="{{ route('medications.destroy', $med) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this medication?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No medications found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-secondary text-dark fw-bold">
                    <td>Total Medications: {{ $totalCount }}</td>
                    <td colspan="2"></td>
                    <td>Total Pack Sizes: {{ $totalPackSize }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>

        </table>

        {{ $medications->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterSelect = document.getElementById('categoryFilter');
            const searchInput = document.getElementById('searchInput');
            let timeout = null;

            const handleFilter = () => {
                if (timeout) clearTimeout(timeout);
                timeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            };

            filterSelect.addEventListener('change', handleFilter);
            searchInput.addEventListener('input', handleFilter);
        });
    </script>
</x-layout>
