<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashAccount;
use Illuminate\Support\Facades\Auth;

class CashAccountController extends Controller
{
    /**
     * List cash/bank accounts with search & filters.
     */
    public function index(Request $request)
    {
        $query = CashAccount::query();

        // search by name or account number
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        // filter by type (cash|bank)
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // active/inactive filter: expected '1' or '0'
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active') ? 1 : 0);
        }

        $accounts = $query->orderBy('name')->paginate(15)->appends($request->only(['search','type','is_active']));

        // compute current balance for display (uses model method)
        foreach ($accounts as $acct) {
            $acct->current_balance = $acct->currentBalance();
        }

        return view('accounting.cash.accounts.index', compact('accounts'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('accounting.cash.accounts.create');
    }

    /**
     * Store new account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cash,bank',
            'opening_balance' => 'nullable|numeric',
            'account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
        ]);

        CashAccount::create(array_merge($request->only([
            'name','type','bank_name','account_number','opening_balance'
        ]), ['created_by' => Auth::id()]));

        return redirect()->route('cash-accounts.index')->with('success','Account created.');
    }

    /**
     * Show edit form.
     */
    public function edit(CashAccount $cashAccount)
    {
        return view('accounting.cash.accounts.edit', compact('cashAccount'));
    }

    /**
     * Update account.
     */
    public function update(Request $request, CashAccount $cashAccount)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cash,bank',
            'opening_balance' => 'nullable|numeric',
            'account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ]);

        $cashAccount->update($request->only(['name','type','bank_name','account_number','opening_balance','is_active']));

        return redirect()->route('cash-accounts.index')->with('success','Account updated.');
    }

    /**
     * Delete account.
     */
    public function destroy(CashAccount $cashAccount)
    {
        $cashAccount->delete();
        return back()->with('success','Account deleted.');
    }
}
