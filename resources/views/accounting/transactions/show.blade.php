@if(request()->ajax() || strpos(url()->current(), '/transactions/') !== false)
    {{-- When fetched via AJAX, show the content only --}}
    <div class="modal-header">
        <h5 class="modal-title">Transaction #{{ $transaction->id ?? '' }}</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <p><strong>Date:</strong> {{ $transaction->date }}</p>
        <p><strong>Type:</strong> {{ ucfirst($transaction->type) }}</p>
        <p><strong>Account:</strong> {{ $transaction->account->code ?? '' }} - {{ $transaction->account->name ?? '' }}</p>
        <p><strong>Amount:</strong> ${{ number_format($transaction->amount,2) }}</p>
        <p><strong>Invoice:</strong> {{ $transaction->invoice ? '#'.$transaction->invoice->id : '—' }}</p>
        <p><strong>Description:</strong> {{ $transaction->description ?? '—' }}</p>
        @if($transaction->receipt_path)
            <p><strong>Receipt:</strong> <a href="{{ Storage::url($transaction->receipt_path) }}" target="_blank">View</a></p>
        @endif
    </div>
@endif
