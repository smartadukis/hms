<x-layout>
    <div class="container">
        <h2 class="text-info mb-4">Patients List</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('patients.create') }}" class="btn btn-info mb-3">+ New Patient</a>

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
                            <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this patient?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
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
</x-layout>
