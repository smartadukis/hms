<x-layout>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7f9;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background: #2c3e50;
            color: #ecf0f1;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 10px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            margin: 0 0 30px 10px;
            font-weight: 700;
        }

        .sidebar nav a {
            display: block;
            color: #bdc3c7;
            text-decoration: none;
            margin: 12px 0;
            padding-left: 15px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .sidebar nav a:hover {
            background: #34495e;
            border-left-color: #e67e22;
            color: #fff;
        }

        .main-content {
            margin-left: 220px;
            padding: 25px 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-weight: 700;
            color: #34495e;
        }

        .header button {
            background: #e67e22;
            border: none;
            color: #fff;
            padding: 10px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .card {
            background: #fff;
            flex: 1;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            padding: 20px;
            min-width: 220px;
        }

        .card h3 {
            margin-top: 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .card .number {
            font-size: 2.2rem;
            font-weight: 700;
            margin-top: 10px;
            color: #e67e22;
        }

        .recent-activities {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }

        .recent-activities h3 {
            margin-top: 0;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .recent-activities ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .recent-activities ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            color: #555;
            font-size: 0.95rem;
        }

        .recent-activities ul li:last-child {
            border: none;
        }
    </style>

    <div class="sidebar">
        <h2>HMS</h2>
        <nav>
            <a href="{{ route('dashboard') }}">Dashboard</a>

            @php $role = auth()->user()->role; @endphp

            @if($role === 'admin')
                <a href="{{ route('admin.users') }}">Manage Users</a>
                <a href="{{ route('admin.settings') }}">Settings</a>
            @endif

            @if(in_array($role, ['admin', 'receptionist']))
                <a href="{{ route('patients.index') }}">Manage Patients</a>
            @endif


            @if($role === 'doctor')
                <a href="{{ route('doctor.patients') }}">My Patients</a>
                <a href="#">Prescriptions</a>
                <a href="#">Lab Requests</a>
            @endif

            @if($role === 'nurse')
                <a href="{{ route('nurse.appointments') }}">Appointments</a>
                <a href="#">Patient Vitals</a>
            @endif

            @if($role === 'receptionist')
                <a href="{{ route('receptionist.register') }}">Register Patients</a>
                <a href="#">Manage Appointments</a>
            @endif

            @if($role === 'lab_staff')
                <a href="{{ route('lab.results') }}">Lab Requests</a>
                <a href="#">Upload Results</a>
            @endif

            @if($role === 'pharmacist')
                <a href="{{ route('pharmacy.prescriptions') }}">Prescriptions</a>
            @endif

            @if($role === 'accountant')
                <a href="{{ route('accountant.billing') }}">Billing & Invoices</a>
            @endif

            @if($role === 'patient')
                <a href="{{ route('patient.records') }}">My Records</a>
            @endif

            <a href="{{ route('logout') }}">Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <button>New Appointment</button>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Total Patients</h3>
                <div class="number">512</div>
            </div>
            <div class="card">
                <h3>Today's Appointments</h3>
                <div class="number">34</div>
            </div>
            <div class="card">
                <h3>Pending Lab Results</h3>
                <div class="number">12</div>
            </div>
        </div>

        <div class="recent-activities">
            <h3>Recent Activities</h3>
            <ul>
                <li>Patient Bob Andrew checked in</li>
                <li>Lab results uploaded for Patient Johnson</li>
                <li>Appointment confirmed for Patient Cynthia</li>
                <li>New prescription added for Patient Mary</li>
            </ul>
        </div>
    </div>
</x-layout>
