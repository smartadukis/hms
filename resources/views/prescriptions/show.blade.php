<x-layout>
    <div class="container">

        <h2 class="mb-3">Prescription Details</h2>

        <!-- Download PDF Button -->
        <div class="text-end mb-3">
            <button class="btn btn-outline-danger" onclick="downloadPDF()">
                Download PDF
            </button>
        </div>

        <!-- PDF Section -->
        <div id="pdf-content" class="bg-white p-4 border rounded shadow-sm">
            <!-- Logo & Title -->
            <div class="d-flex align-items-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Healer HMS Logo" height="50" class="me-3">
                <h3 class="m-0">Healer HMS</h3>
            </div>

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
        </div>
        <!-- /PDF Section -->

        <!-- Action Buttons -->
        @php
            $user = auth()->user();
            $isDoctorOwner = $user->role === 'doctor' && $prescription->doctor_id === $user->id;
            $canManage = in_array($user->role, ['admin', 'receptionist']) || $isDoctorOwner;
        @endphp

        <div class="mt-4 d-flex flex-wrap gap-2">
            <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Back</a>

            @if ($canManage)
                <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('prescriptions.editStatus', $prescription) }}" class="btn btn-info text-white">Change Status</a>
            @endif

            @if(in_array($user->role, ['admin', 'pharmacist']) && $prescription->status !== 'dispensed')
                <form action="{{ route('prescriptions.dispense', $prescription) }}" method="POST" class="m-0">
                    @csrf @method('PUT')
                    <button 
                        type="submit" 
                        class="btn btn-success"
                        onclick="return confirm('Dispense this prescription and update stock?')">
                        Dispense
                    </button>
                </form>
            @endif
        </div>

    </div>

    <!-- html2pdf.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('pdf-content');
            const opt = {
                margin:       0.3,
                filename:     'Prescription-{{ $prescription->id }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().from(element).set(opt).save();
        }
    </script>
</x-layout>
