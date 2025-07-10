<x-layout>
    <div class="container">
        <h2 class="mb-4">Lab Tests</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('lab-tests.create') }}" class="btn btn-info mb-3">+ Request New Lab Test</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Test Type</th>
                    <th>Requested By</th>
                    <th>Doctor</th>
                    <th>Status</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tests as $test)
                    <tr>
                        <td>{{ $test->patient->name }}</td>
                        <td>{{ $test->test_type }}</td>
                        <td>{{ $test->requestedBy->name ?? 'N/A' }}</td>
                        <td>{{ $test->doctor->name }}</td>
                        <td>
                            <span class="badge 
                                @if($test->status == 'pending') bg-secondary
                                @elseif($test->status == 'in_progress') bg-warning
                                @else bg-success
                                @endif">
                                {{ ucfirst($test->status) }}
                            </span>
                        </td>
                        <td>
                            @if($test->result_file)
                                <a href="{{ route('lab-tests.view-file', $test->id) }}" class="btn btn-sm btn-outline-info" target="_blank">View</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('lab-tests.edit', $test) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('lab-tests.destroy', $test) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this test?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No lab tests found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $tests->links() }}
    </div>
</x-layout>
