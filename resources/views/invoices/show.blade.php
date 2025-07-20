<x-layout>
  <div class="container mt-4" id="invoice-section" style="max-width: 800px; background: #fff; padding: 30px; border: 1px solid #ddd;">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <img src="{{ asset('images/logo.png') }}" alt="Hospital Logo" style="height: 60px;">
      <div class="text-end">
        <h4>INVOICE</h4>
        <p><strong>Invoice #:</strong> {{ $invoice->id }}</p>
        <p><strong>Date:</strong> {{ $invoice->created_at->format('d M, Y') }}</p>
      </div>
    </div>

    {{-- Patient Info --}}
    <div class="mb-3">
      <p><strong>Patient Name:</strong> {{ $invoice->patient->name }}</p>
      <p><strong>Status:</strong> 
        <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : 'warning' }}">
          {{ ucfirst($invoice->status) }}
        </span>
      </p>
      @if($invoice->payment_method)
        <p><strong>Payment Method:</strong> {{ $invoice->payment_method }}</p>
      @endif
    </div>

    {{-- Items Table --}}
    <table class="table table-bordered">
      <thead>
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

    <h5 class="text-end mt-3">Total: ${{ number_format($invoice->total_amount, 2) }}</h5>

  </div>

  
  
    {{-- Footer Actions --}}
    <div class="container mt-4">
      <h5>Actions</h5>
      <p>You can download or print this invoice for your records.</p>
    <div class="d-flex justify-content-between mt-4">
      <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back to Invoices</a>
      <button onclick="downloadInvoice()" class="btn btn-success">Download / Print PDF</button>
    </div>
  </div>

  <script>
    function downloadInvoice() {
      const printContents = document.getElementById("invoice-section").innerHTML;
      const original = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = original;
      location.reload();
    }
  </script>
</x-layout>
