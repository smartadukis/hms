{{-- resources/views/partials/_sidebar.blade.php --}}
@php $role = auth()->user()->role; @endphp

<div class="sidebar-top d-flex align-items-center mb-3">
    {{-- inline SVG logo (small) --}}
    <div style="width:38px;height:38px;margin-right:10px;display:flex;align-items:center;justify-content:center;">
        <img src="{{ asset('images/logo.png') }}" alt="Healer HMS" style="width:100%;height:100%;object-fit:contain;">
        <!-- <svg viewBox="0 0 24 24" width="32" height="32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="9" y="3" width="6" height="6" fill="#13cbd8" rx="1.5"/>
            <rect x="3" y="9" width="6" height="6" fill="#13cbd8" rx="1.5"/>
            <rect x="15" y="9" width="6" height="6" fill="#13cbd8" rx="1.5"/>
            <rect x="9" y="15" width="6" height="6" fill="#13cbd8" rx="1.5"/>
        </svg> -->
    </div>

    <div>
        <h2 class="mb-0" style="font-size:1.15rem;color:#ecf0f1;">Healer HMS</h2>
        <small class="text-muted d-block" style="color:#bfc9cc">Hospital Management</small>
    </div>
</div>

<nav class="sidebar-nav">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

    @if($role === 'admin')
        <a href="{{ route('admin.users.index') }}">Manage Users</a>
        <a href="{{ route('patients.index') }}">Manage Patients</a>
        <a href="{{ route('appointments.index') }}">Manage Appointments</a>
        <a href="{{ route('lab-tests.index') }}">Manage Lab Tests</a>
        <a href="{{ route('medications.index') }}">Manage Medications</a>
        <a href="{{ route('invoices.index') }}">Manage Invoices</a>
        <a href="{{ route('transactions.index') }}">Manage Transactions</a>
        <a href="{{ route('accounts.index') }}">Manage Accounts</a>
        <a href="{{ route('journal.index') }}">Manage Journal</a>
    @endif

    @if($role === 'doctor')
        <a href="{{ route('doctor.patients') }}">My Patients</a>
        <a href="{{ route('doctor.appointments') }}">My Appointments</a>
        <a href="{{ route('prescriptions.index') }}">Prescriptions</a>
        <a href="{{ route('lab-tests.index') }}">Lab Requests</a>
    @endif

    @if(in_array($role, ['doctor','lab_staff']))
        <a href="{{ route('lab-tests.index') }}">Manage Lab Tests</a>
    @endif

    @if($role === 'receptionist')
        <a href="{{ route('patients.index') }}">Manage Patients</a>
        <a href="{{ route('appointments.index') }}">Manage Appointments</a>
        <a href="{{ route('invoices.index') }}">Manage Invoices</a>
        <a href="{{ route('lab-tests.index') }}">Manage Lab Tests</a>
    @endif

    <a href="{{ route('prescriptions.index') }}">Manage Prescriptions</a>

    @if($role === 'pharmacist')
        <a href="{{ route('medications.index') }}">Manage Medications</a>
    @endif

    @if($role === 'accountant')
        <a href="{{ route('invoices.index') }}">Manage Invoices</a>
        <a href="{{ route('transactions.index') }}">Manage Transactions</a>
        <a href="{{ route('accounts.index') }}">Manage Accounts</a>
        <a href="{{ route('journal.index') }}">Manage Journal</a>
    @endif

    @if($role === 'patient')
        <a href="{{ route('patients.records') }}">My Records</a>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-link nav-link p-0" style="color:#bdc3c7">Logout</button>
    </form>
</nav>
