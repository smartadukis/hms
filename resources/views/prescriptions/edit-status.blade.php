<x-layout>
  <div class="container">
    <h2 class="mb-4">Update Prescription Status</h2>

    <form action="{{ route('prescriptions.updateStatus', $prescription) }}" method="POST">
      @csrf @method('PUT')

      <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select">
          <option value="pending" {{ $prescription->status=='pending'?'selected':'' }}>Pending</option>
          <option value="partial" {{ $prescription->status=='partial'?'selected':'' }}>Partial</option>
          <option value="dispensed" {{ $prescription->status=='dispensed'?'selected':'' }}>Dispensed</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Notes (e.g. out‑of‑stock items)</label>
        <textarea name="notes" class="form-control">{{ old('notes',$prescription->notes) }}</textarea>
      </div>

      <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Back</a>
      <button class="btn btn-primary">Save</button>
    </form>
  </div>
</x-layout>
