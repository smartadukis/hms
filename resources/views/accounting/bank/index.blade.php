<x-layout>
<div class="container mt-4">
    <h2 class="mb-4">Bank & Cash Transactions</h2>

    {{-- Search/Filter --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="account" value="{{ request('account') }}" class="form-control" placeholder="Bank/Cash Account">
        </div>
        <div class="col-md-2">
            <select name="type" class="form-control">
                <option value="">All Types</option>
                <option value="Deposit" {{ request('type')=='Deposit'?'selected':'' }}>Deposit</option>
                <option value="Withdrawal" {{ request('type')=='Withdrawal'?'selected':'' }}>Withdrawal</option>
                <option value="Transfer" {{ request('type')=='Transfer'?'selected':'' }}>Transfer</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="text" name="narration" value="{{ request('narration') }}" class="form-control" placeholder="Narration">
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    {{-- Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Balance After</th>
                <th>Narration</th>
                <th>Created By</th>
                <th>IP</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bankTxns as $txn)
            <tr>
                <td>{{ $txn->date->format('d M Y') }}</td>
                <td>{{ $txn->account->name }}</td>
                <td>{{ $txn->type }}</td>
                <td>{{ number_format($txn->amount,2) }}</td>
                <td>{{ number_format($txn->balance_after,2) }}</td>
                <td>{{ $txn->narration }}</td>
                <td>{{ $txn->user->name ?? '—' }}</td>
                <td>{{ $txn->ip_address }}</td>
                <td>
                    <a href="{{ route('bank.edit',$txn->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('bank.destroy',$txn->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this entry?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center">No records found.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $bankTxns->links() }}
</div>
</x-layout>
