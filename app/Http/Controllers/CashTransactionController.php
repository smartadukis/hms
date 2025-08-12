<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashTransaction;
use App\Models\CashAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CashTransactionController extends Controller
{
    /**
     * List transactions with search & filters, and compute balance_after per row.
     */
    public function index(Request $request)
    {
        $query = CashTransaction::with('account','relatedAccount','creator');

        // filter by account id or account name
        if ($account = $request->input('account')) {
            // if numeric assume id, otherwise search by account name or account_number
            if (is_numeric($account)) {
                $query->where('cash_account_id', intval($account));
            } else {
                $query->whereHas('account', function ($q) use ($account) {
                    $q->where('name', 'like', "%{$account}%")
                      ->orWhere('account_number', 'like', "%{$account}%");
                });
            }
        }

        // filter by type: deposit/withdrawal/transfer
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // date range
        if ($from = $request->input('from')) {
            $query->whereDate('transaction_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('transaction_date', '<=', $to);
        }

        // narration / description search
        if ($narr = $request->input('narration')) {
            $query->where(function ($q) use ($narr) {
                $q->where('description', 'like', "%{$narr}%")
                  ->orWhere('reference', 'like', "%{$narr}%");
            });
        }

        // fetch accounts for filter dropdown
        $accounts = CashAccount::orderBy('name')->get();

        // paginate
        $transactions = $query->orderBy('transaction_date', 'desc')->orderBy('id','desc')->paginate(20)
                              ->appends($request->only(['account','type','from','to','narration']));

        // compute balance_after for each transaction (simple running sum per account up to that transaction)
        foreach ($transactions as $txn) {
            $acct = $txn->account;
            if (!$acct) {
                $txn->balance_after = null;
                continue;
            }

            // opening balance
            $opening = (float) $acct->opening_balance;

            // sum deposits up to and including this transaction's date & id
            $deposits = CashTransaction::where('cash_account_id', $acct->id)
                ->where(function($q) use ($txn) {
                    $q->whereDate('transaction_date', '<', $txn->transaction_date)
                      ->orWhere(function($q2) use ($txn) {
                          $q2->whereDate('transaction_date', $txn->transaction_date)
                             ->where('id', '<=', $txn->id);
                      });
                })
                ->whereIn('type', ['deposit'])
                ->sum('amount');

            // sum withdrawals up to and including this transaction
            $withdrawals = CashTransaction::where('cash_account_id', $acct->id)
                ->where(function($q) use ($txn) {
                    $q->whereDate('transaction_date', '<', $txn->transaction_date)
                      ->orWhere(function($q2) use ($txn) {
                          $q2->whereDate('transaction_date', $txn->transaction_date)
                             ->where('id', '<=', $txn->id);
                      });
                })
                ->whereIn('type', ['withdrawal'])
                ->sum('amount');

            // Note: transfers are represented as withdrawal + deposit records, so they're already counted appropriately.

            $txn->balance_after = $opening + (float)$deposits - (float)$withdrawals;
        }

        return view('accounting.cash.transactions.index', compact('transactions','accounts'));
    }

    /**
     * Show form to create deposit/withdrawal/transfer.
     */
    public function create()
    {
        $accounts = CashAccount::orderBy('name')->get();
        return view('accounting.cash.transactions.create', compact('accounts'));
    }

    /**
     * Store transaction. For a transfer, create two records (withdrawal + deposit).
     */
    public function store(Request $request)
    {
        $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'type' => 'required|in:deposit,withdrawal,transfer',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'nullable|date',
            'related_account_id' => 'nullable|exists:cash_accounts,id',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('receipt_file')) {
            $filePath = $request->file('receipt_file')->store('receipts', 'public');
        }

        DB::transaction(function () use ($request, $filePath) {
            $data = [
                'amount' => $request->amount,
                'reference' => $request->reference,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date ?: now()->toDateString(),
                'receipt_file' => $filePath,
                'created_by' => Auth::id(),
                'ip_address' => request()->ip(),
            ];

            if ($request->type === 'transfer') {
                // withdraw from source
                CashTransaction::create(array_merge($data, [
                    'cash_account_id' => $request->cash_account_id,
                    'type' => 'withdrawal',
                    'related_account_id' => $request->related_account_id,
                ]));

                // deposit to destination
                CashTransaction::create(array_merge($data, [
                    'cash_account_id' => $request->related_account_id,
                    'type' => 'deposit',
                    'related_account_id' => $request->cash_account_id,
                ]));
            } else {
                CashTransaction::create(array_merge($data, [
                    'cash_account_id' => $request->cash_account_id,
                    'type' => $request->type,
                ]));
            }
        });

        return redirect()->route('cash-transactions.index')->with('success','Transaction recorded.');
    }

    /**
     * Show a single transaction.
     */
    public function show(CashTransaction $cashTransaction)
    {
        $cashTransaction->load('account','relatedAccount','creator');
        return view('accounting.cash.transactions.show', compact('cashTransaction'));
    }

    /**
     *  destroy.
     */
    public function destroy(CashTransaction $cashTransaction)
    {
        // delete file if exists
        if ($cashTransaction->receipt_file) {
            Storage::disk('public')->delete($cashTransaction->receipt_file);
        }
        $cashTransaction->delete();
        return back()->with('success','Transaction deleted.');
    }
}
