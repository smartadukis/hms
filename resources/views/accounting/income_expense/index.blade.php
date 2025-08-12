<x-layout>
<div class="container mt-4">
    <h2 class="mb-4">Income & Expense Records</h2>

    {{-- Search/Filter --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-2">
            <select name="type" class="form-control">
                <option value="">All</option>
                <option value="Income" {{ request('type')=='Income'?'selected':'' }}>Income</option>
                <option value="Expense" {{ request('type')=='Expense'?'selected':'' }}>Expense</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="category" value="{{ request('category') }}" class="form-control" placeholder="Category">
        </div>
        <div class="col-md-2">
            <input type="text" name="department" value="{{ request('department') }}" class="form-control" placeholder="Department">
        </div>
        <div class="col-md-2">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
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
                <th>Type</th>
                <th>Category</th>
                <th>Department</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Receipt</th>
                <th>Created By</th>
                <th>IP</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($records as $rec)
            <tr>
                <td>{{ $rec->date->format('d M Y') }}</td>
                <td>{{ $rec->type }}</td>
                <td>{{ $rec->category }}</td>
                <td>{{ $rec->department }}</td>
                <td>{{ number_format($rec->amount,2) }}</td>
                <td>{{ $rec->description }}</td>
                <td>
                    @if($rec->receipt_path)
                        <a href="{{ asset('storage/'.$rec->receipt_path) }}" target="_blank">View</a>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $rec->user->name ?? '—' }}</td>
                <td>{{ $rec->ip_address }}</td>
                <td>
                    <a href="{{ route('income_expense.edit',$rec->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('income_expense.destroy',$rec->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this record?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="text-center">No records found.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $records->links() }}
</div>
</x-layout>
