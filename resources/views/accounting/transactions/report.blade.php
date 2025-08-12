<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Healer_HMS_Transactions_Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .logo { display:flex; align-items:center; gap:10px; }
    .logo img { height:50px; }
    .report-title { text-align:center; }
    table { width:100%; font-size:0.95rem; }
    .totals { margin-top: 10px; font-weight:700; }
  </style>
</head>
<body>
  <div id="report">
    <div class="header">
      <div class="logo">
        {{-- place logo at public/images/logo.png --}}
        <img src="{{ public_path('images/logo.png') ? asset('images/logo.png') : '' }}" alt="Healer HMS Logo" onerror="this.style.display='none'">
        <div>
          <div><strong>Healer HMS</strong></div>
          <div>Transactions Report</div>
        </div>
      </div>
      <div>
        <div><strong>From:</strong> {{ $fromLabel ?: '—' }}</div>
        <div><strong>To:</strong> {{ $toLabel ?: '—' }}</div>
      </div>
    </div>

    <table class="table table-sm table-bordered">
      <thead class="table-light">
        <tr>
          <th>Date</th><th>Type</th><th>Account</th><th>Amount</th><th>Description</th><th>Invoice</th><th>Recorded By</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactions as $t)
          <tr>
            <td>{{ $t->date }}</td>
            <td>{{ ucfirst($t->type) }}</td>
            <td>{{ $t->account->code ?? '' }} - {{ $t->account->name ?? '' }}</td>
            <td>${{ number_format($t->amount,2) }}</td>
            <td>{{ $t->description }}</td>
            <td>{{ $t->invoice ? '#'.$t->invoice->id : '—' }}</td>
            <td>{{ $t->creator->name ?? '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totals">
      <div>Total Income: ${{ number_format($totalIncome,2) }}</div>
      <div>Total Expense: ${{ number_format($totalExpense,2) }}</div>
      <div>Net: ${{ number_format($totalIncome - $totalExpense,2) }}</div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    // Auto-generate filename
    const from = "{{ $fromLabel ?: 'start' }}";
    const to = "{{ $toLabel ?: 'end' }}";
    const filename = `Healer_HMS_Transactions_${from}_${to}.pdf`;

    window.addEventListener('load', () => {
      const opt = {
        margin: 0.4,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
      };
      const element = document.getElementById('report');
      html2pdf().set(opt).from(element).save();
      // optional: close the window automatically after few seconds
      setTimeout(()=> window.close(), 1500);
    });
  </script>
</body>
</html>
