<x-layout>
    <div class="container">
        <h2 class="mb-3">Prescription Details</h2>

        <p><strong>Patient:</strong> {{ $prescription->patient->name }}</p>
        <p><strong>Doctor:</strong> {{ $prescription->doctor->name }}</p>
        <p><strong>Notes:</strong> {{ $prescription->notes ?? 'None' }}</p>

        <h5 class="mt-4">Medications</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $item)
                    <tr>
                        <td>{{ $item->medication->name }}</td>
                        <td>{{ $item->dosage_quantity }} {{ $item->dosage_unit }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->duration }}</td>
                        <td>{{ $item->instructions ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Back</a>
    </div>
</x-layout>
