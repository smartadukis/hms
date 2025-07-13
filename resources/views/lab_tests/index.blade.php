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
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-action="{{ route('lab-tests.destroy', $test) }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No lab tests found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $tests->links() }}
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
                        Are you sure you want to delete this lab test? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JS to handle modal dynamic action --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const confirmModal = document.getElementById('confirmDeleteModal');
            const deleteForm = document.getElementById('deleteForm');

            confirmModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                deleteForm.setAttribute('action', action);
            });
        });
    </script>
</x-layout>
