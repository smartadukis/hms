<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;

/******************************************************************************************
 * Basic Routes
 ****************************************************************************************** */

// Redirect root to dashboard

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


/******************************************************************************************
 * Dashboard Routes
 ******************************************************************************************/
// This section contains routes that are shared across different user roles.

 // Shared dashboard
Route::middleware(['auth'])->group(function () {

    // Shared dashboard view
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', fn() => view('admin.users'))->name('admin.users');
        Route::get('/admin/settings', fn() => view('admin.settings'))->name('admin.settings');
    });

    
    // Doctor
    Route::middleware('role:doctor')->group(function () {
    Route::get('/doctor/patients', fn() => view('doctor.patients'))->name('doctor.patients');

    Route::get('/doctor/appointments', [AppointmentController::class, 'doctorAppointments'])->name('doctor.appointments');
    Route::get('/doctor/appointments/{appointment}/edit', [AppointmentController::class, 'editDoctor'])->name('doctor.appointments.edit');
    Route::put('/doctor/appointments/{appointment}', [AppointmentController::class, 'updateDoctor'])->name('doctor.appointments.update');
    
    });


    // Nurse
    Route::middleware('role:nurse')->group(function () {
        Route::get('/nurse/appointments', fn() => view('nurse.appointments'))->name('nurse.appointments');
    });

    // Receptionist
    Route::middleware('role:receptionist')->group(function () {
        Route::get('/receptionist/register', fn() => view('receptionist.register'))->name('receptionist.register');
    });

    // Laboratory Staff
    Route::middleware('role:lab_staff')->group(function () {
        Route::get('/lab/results', fn() => view('lab.results'))->name('lab.results');
    });

    // Pharmacist
    Route::middleware('role:pharmacist')->group(function () {
        Route::get('/pharmacy/prescriptions', fn() => view('pharmacy.prescriptions'))->name('pharmacy.prescriptions');
    });

    // Accountant
    Route::middleware('role:accountant')->group(function () {
        Route::get('/accountant/billing', fn() => view('accountant.billing'))->name('accountant.billing');
    });

    // Patient
    Route::middleware('role:patient')->group(function () {
        Route::get('/patients/records', fn() => view('patients.records'))->name('patients.records');
    });
});

/******************************************************************************************
 * Patient Routes
 ****************************************************************************************** */


Route::middleware(['auth'])->group(function () {
    Route::resource('patients', PatientController::class);
});

/*******************************************************************************************
 * Appointment Routes
 ***************************************************************************************** */
 
 Route::middleware(['auth'])->group(function () {
    Route::resource('appointments', AppointmentController::class);
});

/******************************************************************************************
 * Lab Test Routes
 ******************************************************************************************/

 // Lab Test Routes (shared for now, can later separate per role)
Route::middleware(['auth'])->group(function () {
    Route::resource('lab-tests', LabTestController::class);
});

// Route to view lab test result file
Route::get('/lab-tests/view-file/{id}', [LabTestController::class, 'viewFile'])->name('lab-tests.view-file');


