<?php
// app/Http/Controllers/AccountController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

/**
 * AccountController
 *
 * Handles the management of Chart of Accounts including listing, searching, filtering,
 * creating, updating, and deleting accounts.
 *
 * @package App\Http\Controllers
 */
class AccountController extends Controller
{
    /**
     * Display a listing of accounts with optional search, type, and status filters.
     *
     * @param \Illuminate\Http\Request $request HTTP request containing optional filters
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Account::query();

        // Search filter (by name or code)
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

        // Filter by active/inactive status
        if (($status = $request->input('status')) !== null && $status !== '') {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Order by type and paginate results (preserving filters in query string)
        $accounts = $query->orderBy('type')->paginate(25)
                          ->appends($request->only(['search','type','status']));

        // Predefined account types
        $types = ['Asset','Liability','Income','Expense','Equity'];

        return view('accounting.accounts.index', compact('accounts','types'));
    }

    /**
     * Show the details of a specific account.
     *
     * @param \App\Models\Account $account Account instance injected via route model binding
     * @return \Illuminate\View\View
     */
    public function show(Account $account)
    {
        return view('accounting.accounts.show', compact('account'));
    }

    /**
     * Store a newly created account.
     *
     * @param \Illuminate\Http\Request $request HTTP request with account details
     * @return \Illuminate\Http\RedirectResponse Redirects to account list with success message
     */
    public function store(Request $request)
    {
        // Validate incoming account data
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:accounts,code',
            'type' => 'required|in:Asset,Liability,Income,Expense,Equity',
            'is_active' => 'nullable|in:0,1',
            'description' => 'nullable|string',
        ]);

        // Extract relevant data
        $data = $request->only(['name','code','type','description']);

        // Default to active if 'is_active' is not set
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        Account::create($data);

        return redirect()->route('accounts.index')->with('success','Account created.');
    }

    /**
     * Update an existing account.
     *
     * @param \Illuminate\Http\Request $request HTTP request containing updated account data
     * @param \App\Models\Account $account Account instance to update
     * @return \Illuminate\Http\RedirectResponse Redirects to account list with success message
     */
    public function update(Request $request, Account $account)
    {
        // Validate with exclusion for the current account ID (unique constraint)
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:accounts,code,' . $account->id,
            'type' => 'required|in:Asset,Liability,Income,Expense,Equity',
            'is_active' => 'nullable|in:0,1',
            'description' => 'nullable|string',
        ]);

        // Extract relevant data
        $data = $request->only(['name','code','type','description']);

        // Keep existing 'is_active' value if not provided
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : $account->is_active;

        $account->update($data);

        return redirect()->route('accounts.index')->with('success','Account updated.');
    }

    /**
     * Delete an account.
     *
     * @param \App\Models\Account $account Account instance to delete
     * @return \Illuminate\Http\RedirectResponse Redirects back with success message
     */
    public function destroy(Account $account)
    {
        $account->delete();
        return back()->with('success','Account deleted.');
    }
}
