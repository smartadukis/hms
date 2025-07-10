<x-layout>
    <div class="container">
        <h2 class="mb-4">Update Lab Test</h2>

        <form action="{{ route('lab-tests.update', $labTest) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ $labTest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $labTest->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $labTest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                </select>
            </div>

            <div class="mb-3">
                <label for="result_file" class="form-label">Upload Result (PDF/Image)</label>
                <input type="file" name="result_file" class="form-control">
                @if($labTest->result_file)
                    <small>
                        <a href="{{ Storage::url($labTest->result_file) }}" target="_blank">Download Existing File</a>
                    </small>
                @endif
                @error('result_file') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Result (optional)</label>
                <textarea name="result" class="form-control" rows="4">{{ $labTest->result }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Lab Test</button>
            <a href="{{ route('lab-tests.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-layout>
