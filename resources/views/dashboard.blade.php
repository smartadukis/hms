<x-layout>
  <div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-none d-md-block">
      <h4 class="text-light">HMS</h4>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Patients</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Appointments</a></li>
        <!-- ... other links ... -->
      </ul>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
      <div class="header d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-info">Dashboard</h2>
        <button class="btn btn-aqua">New Appointment</button>
      </div>

      <!-- Placeholder cards -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="card p-3">
            <h5>Total Patients</h5>
            <h1 class="text-aqua">512</h1>
          </div>
        </div>
        <!-- ... -->
      </div>

      <!-- Recent Activities -->
      <div class="recent-activities p-3">
        <h5>Recent Activities</h5>
        <ul class="list-unstyled mb-0">
          <li>Patient Bob Andrew checked in</li>
          <!-- ... -->
        </ul>
      </div>
    </div>
  </div>
</x-layout>
