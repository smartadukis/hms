{{-- resources/views/accounting/transactions/edit.blade.php --}}
{{-- This view returns a modal-content block for AJAX injection into the edit modal. --}}
@php /** @var \App\Models\Transaction $transaction */ @endphp

<div class="modal-header">
    <h5 class="modal-title">Edit Transaction #{{ $transaction->id }}</h5>
    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('transactions.update', $transaction) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-body">
        {{-- reuse the form partial (ensure the partial expects $transaction and $accounts) --}}
        @include('accounting.transactions._form', ['transaction' => $transaction, 'accounts' => $accounts])
    </div>

    <div class="modal-footer">
        <!-- Cancel must be button so it doesn't submit -->
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
