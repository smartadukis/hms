<x-layout>
  <div class="container my-4" id="invoice-section">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <img src="{{ asset('images/logo.png') }}" alt="Hospital Logo" height="70">
        <h4 class="mt-2">HEALER Hospital</h4>
        <p>123 Health Ave, Abuja<br>+234-800-0000</p>
      </div>
      <div class="text-end">
        <h3>INVOICE</h3>
        <p><strong>Invoice ID:</strong> #{{ $invoice->id }}</p>
        <p><strong>Date:</strong> {{ $invoice->created_at->format('d M, Y') }}</p>
      </div>
    </div>

    {{-- Patient & Status Info --}}
    <div class="row mb-4">
      <div class="col-md-6">
        <p><strong>Billed To:</strong><br>{{ $invoice->patient->name }}<br>{{ $invoice->patient->email }}</p>
      </div>
      <div class="col-md-6 text-end">
        <p><strong>Status:</strong> 
          <span class="badge 
            @if($invoice->status == 'paid') bg-success 
            @elseif($invoice->status == 'partial') bg-warning 
            @else bg-danger 
            @endif">
            {{ ucfirst($invoice->status) }}
          </span>
        </p>
        @if($invoice->payment_method)
          <p><strong>Payment Method:</strong> {{ $invoice->payment_method }}</p>
        @endif
        @if($invoice->issued_by)
          <p><strong>Issued By:</strong> {{ $invoice->issuer->name ?? 'N/A' }}</p>
        @endif
      </div>
    </div>

    {{-- Table --}}
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Description</th>
          <th>Amount ($)</th>
          <th>Type</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoice->items as $item)
        <tr>
          <td>{{ $item->description }}</td>
          <td>${{ number_format($item->amount, 2) }}</td>
          <td>{{ ucfirst($item->item_type ?? '-') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Total --}}
    <div class="text-end">
      <h4>Total: ${{ number_format($invoice->total_amount, 2) }}</h4>
    </div>
  </div>

  {{-- Actions --}}
  <div class="container d-flex justify-content-between my-4">
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">← Back</a>
    <button onclick="downloadInvoice()" class="btn btn-success">Download / Print PDF</button>
  </div>

  {{-- JavaScript --}}
  <script>
    function downloadInvoice() {
      const original = document.body.innerHTML;
      const invoiceContent = document.getElementById("invoice-section").innerHTML;
      const win = window.open('', '', 'height=700,width=900');
      win.document.write('<html><head><title>Invoice_{{ $invoice->id }}</title>');
      win.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
      win.document.write('</head><body>');
      win.document.write(invoiceContent);
      win.document.write('</body></html>');
      win.document.close();
      win.print();
    }
  </script>
</x-layout>
