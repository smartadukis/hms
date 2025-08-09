<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

/**
 * Controller for Chart of Accounts (CRUD + listing with search/filter)
 */
class AccountController extends Controller
{
    /**
     * Display a listing of the resource with search, type and status filters.
     */
    public function index(Request $request)
    {
        $query = Account::query();

        // Search by name or code
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by account type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Filter by active status: 'active', 'inactive' or null for all
        if (($status = $request->input('status')) !== null && $status !== '') {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // ordering & pagination (preserve filters)
        $accounts = $query->orderBy('type')->paginate(25)
                          ->appends($request->only(['search','type','status']));

        $types = ['Asset','Liability','Income','Expense','Equity'];

        return view('accounting.accounts.index', compact('accounts','types'));
    }


    /**
     * Show the specified account (if needed as separate page).
     */
    public function show(Account $account)
    {
        return view('accounting.accounts.show', compact('account'));
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:accounts,code',
            'type' => 'required|in:Asset,Liability,Income,Expense,Equity',
            'is_active' => 'nullable|in:0,1',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['name','code','type','description']);
        // default to active if not provided
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        Account::create($data);

        return redirect()->route('accounts.index')->with('success','Account created.');
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:accounts,code,' . $account->id,
            'type' => 'required|in:Asset,Liability,Income,Expense,Equity',
            'is_active' => 'nullable|in:0,1',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['name','code','type','description']);
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : $account->is_active;

        $account->update($data);

        return redirect()->route('accounts.index')->with('success','Account updated.');
    }


    /**
     * Remove the specified account from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();
        return back()->with('success','Account deleted.');
    }
}
