{{-- resources/views/dashboard.blade.php --}}
<x-layout>
    @php
        $role = auth()->user()->role;
        // safe fallbacks for counts (controller should pass these)
        $counts = $counts ?? [];
        $roleData = $roleData ?? [];
        $c = fn($k,$d=0) => $counts[$k] ?? $d;
    @endphp

    <style>
        /* container + sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #2c3e50;
            color: #ecf0f1;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 220px;
            padding: 28px 30px;
            min-height: 100vh;
            background: #f4f7f9;
        }

        /* sidebar links */
        .sidebar .sidebar-nav a {
            display:block;
            color:#bdc3c7;
            text-decoration:none;
            margin:8px 0;
            padding:8px 10px;
            border-left:3px solid transparent;
            border-radius:4px;
            font-weight:600;
        }
        .sidebar .sidebar-nav a.active,
        .sidebar .sidebar-nav a:hover {
            background:#34495e;
            color:#fff;
            border-left-color:#17a2b8;
        }
        .sidebar .sidebar-top { display:flex; align-items:center; gap:10px; margin-bottom:12px; }

        /* stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            grid-gap: 16px;
            align-items: stretch;
        }
        .stat-card {
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            min-height: 110px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }
        .stat-card h3 { margin:0; font-size:1.05rem; font-weight:700; color:rgba(255,255,255,0.95); }
        .stat-card .number { font-size:2.1rem; font-weight:700; margin-top:8px; color: white; }

        /* light cards */
        .stat-light { background: #fff; color:#222; }
        .stat-light h3, .stat-light .number { color: #222; }

        /* colored */
        .bg-dark { background:#1f2a2f; }
        .bg-secondary { background:#6c7277; }
        .bg-warning { background:#f0ad4e; color:#fff; }
        .bg-primary { background:#1e7be6; }
        .bg-success { background:#1e8f66; }
        .bg-info { background:#11c1e8; color:#fff; }
        .bg-danger { background:#d9534f; }

        /* small meta */
        .meta { font-size:0.85rem; color:rgba(255,255,255,0.85); margin-top:6px; }
        .stat-light .meta { color:#666; }

        /* role-data cards in grid row */
        .role-row { margin-top:18px; }
        .role-card { border-radius:8px; padding:14px; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.06); }

        /* responsive: collapse sidebar on small screens */
        @media (max-width: 767px) {
            .sidebar { position:relative; width:100%; height:auto; padding:12px 14px; }
            .main-content { margin-left:0; padding:16px; }
            .sidebar .sidebar-nav { display:flex; flex-wrap:wrap; gap:6px; }
            .sidebar .sidebar-nav a { padding:6px 8px; font-size:0.95rem; }
        }

        /* small helpers */
        .btn-space { margin-left:10px; }
    </style>

    <div class="sidebar">
        @include('partials._sidebar')
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h2 style="margin-bottom:6px;">Welcome, {{ auth()->user()->name }}</h2>
                <p class="text-muted" style="margin:0;">This is your dashboard. More features coming soon.</p>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('appointments.create') }}" class="btn btn-info">New Appointment</a>
            </div>
        </div>

        {{-- Main stats grid --}}
        <div class="stats-grid">

            {{-- show admin-only cards first if admin --}}
            @if($role === 'admin')
                <div class="stat-card bg-dark">
                    <div>
                        <h3>Total Patients</h3>
                        <div class="number">{{ $c('patients',0) }}</div>
                    </div>
                </div>

                <div class="stat-card bg-secondary">
                    <div>
                        <h3>Medications</h3>
                        <div class="number">{{ $c('medications',0) }}</div>
                    </div>
                </div>

                <div class="stat-card bg-warning">
                    <div>
                        <h3>Invoices</h3>
                        <div class="number">{{ $c('invoices',0) }}</div>
                    </div>
                </div>

                <div class="stat-card bg-primary">
                    <div>
                        <h3>Accounts</h3>
                        <div class="number">{{ $c('accounts',0) }}</div>
                    </div>
                </div>

                <div class="stat-card bg-success">
                    <div>
                        <h3>Journal Entries</h3>
                        <div class="number">{{ $c('journal_entries',0) }}</div>
                    </div>
                </div>

                <div class="stat-card bg-info">
                    <div>
                        <h3>Prescriptions</h3>
                        <div class="number">{{ $c('prescriptions',0) }}</div>
                    </div>
                </div>
            @endif

            {{-- Appointments --}}
            <div class="stat-card stat-light">
                <div>
                    <h3>Today's Appointments</h3>
                    <div class="number" style="color:#111;">{{ $c('appointments_today',0) }}</div>
                    <div class="meta" style="color:#666;">Pending: {{ $c('appointments_pending',0) }}</div>
                </div>
            </div>

            {{-- Lab --}}
            <div class="stat-card stat-light">
                <div>
                    <h3>Lab - Pending</h3>
                    <div class="number" style="color:#111;">{{ $c('lab_pending',0) }}</div>
                    <div class="meta" style="color:#666;">In Progress: {{ $c('lab_in_progress',0) }} • Completed: {{ $c('lab_completed',0) }}</div>
                </div>
            </div>

            {{-- Total Income --}}
            <div class="stat-card bg-success">
                <div>
                    <h3>Total Income</h3>
                    <div class="number">${{ number_format($c('transactions_income_total',0), 2) }}</div>
                    <div class="meta">Count: {{ $c('transactions_income_count',0) }}</div>
                </div>
            </div>

            {{-- Total Expense --}}
            <div class="stat-card bg-danger">
                <div>
                    <h3>Total Expense</h3>
                    <div class="number">${{ number_format($c('transactions_expense_total',0), 2) }}</div>
                    <div class="meta">Count: {{ $c('transactions_expense_count',0) }}</div>
                </div>
            </div>
        </div>

        {{-- role-specific small cards row --}}
        @if(!empty($roleData))
            <div class="row role-row">
                @foreach($roleData as $k => $v)
                    <div class="col-sm-6 col-md-3 mb-3">
                        <div class="role-card">
                            <h4 class="mb-1 text-capitalize">{{ str_replace('_',' ',$k) }}</h4>
                            <div class="number" style="color:#111;">{{ $v }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-layout>
