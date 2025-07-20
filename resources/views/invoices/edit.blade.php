<x-layout>
    <div class="container">
        <h2 class="mb-4">Edit Invoice #{{ $invoice->id }}</h2>

        <form action="{{ route('invoices.update', $invoice) }}" method="POST">
            @csrf @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Patient</label>
                    <select name="patient_id" class="form-select" required>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id',$invoice->patient_id)==$p->id?'selected':'' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="unpaid"   {{ $invoice->status=='unpaid'?'selected':'' }}>Unpaid</option>
                        <option value="partial"  {{ $invoice->status=='partial'?'selected':'' }}>Partial</option>
                        <option value="paid"     {{ $invoice->status=='paid'   ?'selected':'' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Payment Method (opt.)</label>
                    <input type="text" name="payment_method" class="form-control"
                        value="{{ old('payment_method',$invoice->payment_method) }}">
                </div>
            </div>

            <h5>Items</h5>
            <table class="table" id="items-table">
                <thead>
                    <tr><th>Description</th><th>Amount</th><th>Type</th><th></th></tr>
                </thead>
                <tbody>
                    @php $idx = 0; @endphp
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <input type="text" name="items[{{ $idx }}][description]"
                                       class="form-control" required
                                       value="{{ old("items.{$idx}.description",$item->description) }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[{{ $idx }}][amount]"
                                       class="form-control" required
                                       value="{{ old("items.{$idx}.amount",$item->amount) }}">
                            </td>
                            <td>
                                <select name="items[{{ $idx }}][item_type]" class="form-select">
                                    <option value="">--</option>
                                    <option value="test"        {{ $item->item_type=='test'?'selected':'' }}>Test</option>
                                    <option value="medication"  {{ $item->item_type=='medication'?'selected':'' }}>Medication</option>
                                    <option value="treatment"   {{ $item->item_type=='treatment'?'selected':'' }}>Treatment</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                            </td>
                        </tr>
                        @php $idx++; @endphp
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn btn-sm btn-secondary" id="add-row">+ Add Item</button>
                <div>
                    <button href="{{ route('invoices.index') }}" class="btn btn-dark">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                </div>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let index = {{ $invoice->items->count() }};
            const table = document.getElementById('items-table').querySelector('tbody');

            document.getElementById('add-row').addEventListener('click', () => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="text" name="items[${index}][description]" class="form-control" required></td>
                    <td><input type="number" step="0.01" name="items[${index}][amount]" class="form-control" required></td>
                    <td>
                        <select name="items[${index}][item_type]" class="form-select">
                            <option value="">--</option>
                            <option value="test">Test</option>
                            <option value="medication">Medication</option>
                            <option value="treatment">Treatment</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                `;
                table.appendChild(row);
                index++;
            });

            document.addEventListener('click', function(e){
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
</x-layout>
