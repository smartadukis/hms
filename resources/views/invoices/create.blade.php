<x-layout>
    <div class="container">
      <h2 class="mb-4">Create Invoice</h2>

    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="patient_id">Select Patient</label>
            <select name="patient_id" class="form-control" required>
                <option value="">-- Choose Patient --</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>

        <h5>Invoice Items</h5>
        <table class="table" id="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount ($)</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="items[0][description]" class="form-control" required></td>
                    <td><input type="number" step="0.01" name="items[0][amount]" class="form-control" required></td>
                    <td>
                        <select name="items[0][item_type]" class="form-control">
                            <option value="">--</option>
                            <option value="test">Test</option>
                            <option value="medication">Medication</option>
                            <option value="treatment">Treatment</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                </tr>
            </tbody>
        </table>

       <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="button" class="btn btn-sm btn-secondary" id="add-row">+ Add Item</button>
            <div>
                <a href="{{ route('invoices.index') }}" class="btn btn-dark">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Invoice</button>
            </div>
        </div>

    </form>
    </div>

    <script>
        let index = 1;
        document.getElementById('add-row').addEventListener('click', () => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="items[${index}][description]" class="form-control" required></td>
                <td><input type="number" step="0.01" name="items[${index}][amount]" class="form-control" required></td>
                <td>
                    <select name="items[${index}][item_type]" class="form-control">
                        <option value="">--</option>
                        <option value="test">Test</option>
                        <option value="medication">Medication</option>
                        <option value="treatment">Treatment</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
            `;
            document.querySelector('#items-table tbody').appendChild(row);
            index++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
</x-layout>
