<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display list of transactions with filters/search.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('account','creator');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('description','like', "%{$search}%")
                  ->orWhere('amount','like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($account = $request->input('account_id')) {
            $query->where('account_id', $account);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('date', '<=', $to);
        }

        $transactions = $query->orderBy('date','desc')->paginate(15)
                              ->appends($request->only(['search','type','account_id','from','to']));

        $accounts = Account::orderBy('name')->get();

        return view('accounting.transactions.index', compact('transactions','accounts'));
    }

    /**
     * Store a new transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'type' => 'required|in:income,expense',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'invoice_id' => 'nullable|string|max:50',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);


        $data = $request->only(['date','type','account_id','amount','description','invoice_id']);
        $data['created_by'] = Auth::id();

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = 'txn_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('transactions', $filename, 'public');
            $data['receipt_path'] = $path;
        }

        Transaction::create($data);

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded.');
    }

    /**
     * Return a transaction (for modal view).
     */
    public function show(Transaction $transaction)
    {
        return view('accounting.transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing a transaction.
     */

    public function edit(Transaction $transaction)
{
    $accounts = Account::orderBy('code')->get();

    if (request()->ajax()) {
        return view('accounting.transactions.edit', compact('transaction','accounts'));
    }

    // fallback for normal page load 
    return view('accounting.transactions.edit', compact('transaction','accounts'));
}

    /**
     * Update a transaction.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'date' => 'nullable|date',
            'type' => 'required|in:income,expense',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'invoice_id' => 'nullable|string|max:50', // changed from exists:invoices,id
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);


        $data = $request->only(['date','type','account_id','amount','description','invoice_id']);

        if ($request->hasFile('receipt')) {
            // delete old
            if ($transaction->receipt_path) Storage::disk('public')->delete($transaction->receipt_path);
            $file = $request->file('receipt');
            $filename = 'txn_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('transactions', $filename, 'public');
            $data['receipt_path'] = $path;
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated.');
    }

    /**
     * Delete a transaction.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->receipt_path) Storage::disk('public')->delete($transaction->receipt_path);
        $transaction->delete();
        return back()->with('success', 'Transaction deleted.');
    }

    /**
     * Report view for a date range (used by the Print PDF flow).
     */
    public function report(Request $request)
    {
        $query = Transaction::with('account','creator');

        if ($from = $request->input('from')) $query->whereDate('date','>=',$from);
        if ($to = $request->input('to')) $query->whereDate('date','<=',$to);
        if ($type = $request->input('type')) $query->where('type',$type);

        $transactions = $query->orderBy('date','desc')->get();

        $totalIncome = $transactions->where('type','income')->sum('amount');
        $totalExpense = $transactions->where('type','expense')->sum('amount');

        $fromLabel = $request->input('from') ?: '';
        $toLabel = $request->input('to') ?: '';

        return view('accounting.transactions.report', compact('transactions','totalIncome','totalExpense','fromLabel','toLabel'));
    }
}
