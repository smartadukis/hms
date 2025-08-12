<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\LabTest;
use App\Models\Medication;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Prescription;

class DashboardController extends Controller
{
    /**
     * Show dashboard with role-aware statistics.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // Shared counts (global)
        $counts = [
            'patients' => Patient::count(),
            'medications' => Medication::count(),
            'invoices' => Invoice::count(),
            'accounts' => Account::count(),
            'journal_entries' => JournalEntry::count(),
            'prescriptions' => Prescription::count(),
        ];

        // Appointments
        $counts['appointments_today'] = Appointment::whereDate('scheduled_at', now()->toDateString())->count();
        $counts['appointments_pending'] = Appointment::where('status', 'pending')->count();

        // Lab tests
        $counts['lab_pending'] = LabTest::where('status', 'pending')->count();
        $counts['lab_in_progress'] = LabTest::where('status', 'in_progress')->count();
        $counts['lab_completed'] = LabTest::where('status', 'completed')->count();

        // Transactions: totals (sum) and counts split by type
        $counts['transactions_income_total'] = Transaction::where('type', 'income')->sum('amount');
        $counts['transactions_expense_total'] = Transaction::where('type', 'expense')->sum('amount');
        $counts['transactions_income_count'] = Transaction::where('type', 'income')->count();
        $counts['transactions_expense_count'] = Transaction::where('type', 'expense')->count();

        // Role-specific summaries: doctors/nurses/etc
        $roleData = [];

        if ($role === 'doctor') {
            $roleData['my_patients'] = Prescription::where('doctor_id', $user->id)->distinct('patient_id')->count('patient_id');
            $roleData['my_appointments'] = Appointment::where('doctor_id', $user->id)->count();
            $roleData['my_prescriptions'] = Prescription::where('doctor_id', $user->id)->count();
        } elseif ($role === 'pharmacist') {
            // pharmacist cares about stock and pending prescriptions
            $roleData['medications_low_stock'] = Medication::where('quantity', '<=', 5)->count(); // tweak threshold as needed
        } elseif ($role === 'receptionist') {
            $roleData['todays_checkins'] = Appointment::whereDate('scheduled_at', now()->toDateString())->count();
        } elseif ($role === 'accountant') {
            $roleData['unpaid_invoices'] = Invoice::where('status', 'unpaid')->count();
        }

        return view('dashboard', [
            'counts' => $counts,
            'roleData' => $roleData,
            'role' => $role,
        ]);
    }
}
