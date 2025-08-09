<x-layout>
<div class="container mt-4">
    <h3>Journal Entry #{{ $entry->id }}</h3>
    <p><strong>Date:</strong> {{ $entry->entry_date }}</p>
    <p><strong>Reference:</strong> {{ $entry->reference }}</p>
    <p><strong>Description:</strong> {{ $entry->description }}</p>

    <table class="table table-bordered mt-3">
        <thead><tr><th>Account</th><th>Narration</th><th>Debit</th><th>Credit</th></tr></thead>
        <tbody>
            @php $td=0; $tc=0; @endphp
            @foreach($entry->lines as $l)
                <tr>
                    <td>{{ $l->account->code ?? '' }} - {{ $l->account->name ?? '' }}</td>
                    <td>{{ $l->narration }}</td>
                    <td>{{ number_format($l->debit,2) }}</td>
                    <td>{{ number_format($l->credit,2) }}</td>
                </tr>
                @php $td += $l->debit; $tc += $l->credit; @endphp
            @endforeach
            <tr class="table-light">
                <td colspan="2" class="text-end"><strong>Total</strong></td>
                <td><strong>{{ number_format($td,2) }}</strong></td>
                <td><strong>{{ number_format($tc,2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('journal.index') }}" class="btn btn-secondary">Back</a>
</div>
</x-layout>
