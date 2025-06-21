<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;

// Redirect root to dashboard

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


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
        Route::get('/patient/records', fn() => view('patient.records'))->name('patient.records');
    });
});



