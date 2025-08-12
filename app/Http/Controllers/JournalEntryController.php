<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    /**
     * List journal entries with pagination.
     */
    public function index(Request $request)
    {
        $query = JournalEntry::with('creator');

        // Search (reference OR description)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($from = $request->input('from')) {
            $query->whereDate('entry_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('entry_date', '<=', $to);
        }

        // Created by filter (user id)
        if ($creator = $request->input('created_by')) {
            $query->where('created_by', $creator);
        }

        // Approved status filter: 'any' or exact 0/1
        if (($approved = $request->input('approved')) !== null && $approved !== '') {
            if ($approved === '1' || $approved === '0') {
                $query->where('approved', $approved);
            }
        }

        $entries = $query->orderBy('entry_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(15)
                        ->appends($request->only(['search','from','to','created_by','approved']));

        // creators dropdown (finance users or all users) — you can scope this later
        $creators = \App\Models\User::orderBy('name')->pluck('name','id');

        return view('accounting.journal.index', compact('entries','creators'));
    }

    /**
     * Show form to create a new journal entry.
     */
    public function create()
    {
        $accounts = Account::orderBy('code')->get();
        return view('accounting.journal.create', compact('accounts'));
    }

    /**
     * Store a new journal entry and its lines.
     * Ensures debits == credits and each non-empty line is valid.
     */
    public function store(Request $request)
    {
        // Basic header-level validation (don't require every line field here)
        $request->validate([
            'entry_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.narration' => 'nullable|string',
        ]);

        $lines = $request->input('lines', []);

        $normalized = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($lines as $i => $line) {
            $accountId = $line['account_id'] ?? null;
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
            $narration = $line['narration'] ?? null;

            // skip entirely empty template rows
            if (empty($accountId) && $debit == 0 && $credit == 0 && empty($narration)) {
                continue;
            }

            // account_id is required for non-empty lines
            if (empty($accountId) || !\App\Models\Account::where('id', $accountId)->exists()) {
                return back()->withInput()->withErrors(["lines.$i.account_id" => "Please select a valid account for line " . ($i+1)]);
            }

            // at least one of debit or credit must be > 0
            if ($debit <= 0 && $credit <= 0) {
                return back()->withInput()->withErrors(["lines.$i" => "Line ".($i+1)." must have either a debit or a credit amount."]);
            }

            // cannot have both debit and credit > 0 on same line
            if ($debit > 0 && $credit > 0) {
                return back()->withInput()->withErrors(["lines.$i" => "Line ".($i+1)." cannot have both debit and credit amounts."]);
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $normalized[] = [
                'account_id' => $accountId,
                'debit' => number_format($debit, 2, '.', ''),
                'credit' => number_format($credit, 2, '.', ''),
                'narration' => $narration,
            ];
        }

        if (count($normalized) < 2) {
            return back()->withInput()->withErrors(['lines' => 'Please provide at least two non-empty lines.']);
        }

        // Balanced check
        if (number_format($totalDebit, 2, '.', '') !== number_format($totalCredit, 2, '.', '')) {
            return back()->withInput()->withErrors(['balance' => 'Journal entry must be balanced. Total debits must equal total credits.']);
        }

        \DB::transaction(function () use ($request, $normalized) {
            $entry = \App\Models\JournalEntry::create([
                'entry_date' => $request->entry_date ?? now()->toDateString(),
                'reference' => $request->reference,
                'description' => $request->description,
                'created_by' => auth()->id(),
                'approved' => false,
            ]);

            foreach ($normalized as $ln) {
                $entry->lines()->create($ln);
            }
        });

        return redirect()->route('journal.index')->with('success', 'Journal entry recorded.');
    }

    /**
     * Display a single journal entry with lines.
     */
    public function show(JournalEntry $journal)
    {
        $journal->load('lines.account','creator');
        return view('accounting.journal.show', ['entry' => $journal]);
    }

    /**
     * Show edit form. (Basic implementation – editing lines can be done here.)
     */
    public function edit(JournalEntry $journal)
    {
        $journal->load('lines');
        $accounts = Account::orderBy('code')->get();
        return view('accounting.journal.edit', compact('journal','accounts'));
    }

    /**
     * Update journal entry and lines. Re-validates balancing.
     */
    public function update(Request $request, JournalEntry $journal)
    {
        $request->validate([
            'entry_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.narration' => 'nullable|string',
        ]);

        $lines = $request->input('lines', []);
        $normalized = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($lines as $i => $line) {
            $accountId = $line['account_id'] ?? null;
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
            $narration = $line['narration'] ?? null;

            if (empty($accountId) && $debit == 0 && $credit == 0 && empty($narration)) {
                continue;
            }

            if (empty($accountId) || !\App\Models\Account::where('id', $accountId)->exists()) {
                return back()->withInput()->withErrors(["lines.$i.account_id" => "Please select a valid account for line " . ($i+1)]);
            }

            if ($debit <= 0 && $credit <= 0) {
                return back()->withInput()->withErrors(["lines.$i" => "Line ".($i+1)." must have either a debit or a credit amount."]);
            }

            if ($debit > 0 && $credit > 0) {
                return back()->withInput()->withErrors(["lines.$i" => "Line ".($i+1)." cannot have both debit and credit amounts."]);
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $normalized[] = [
                'account_id' => $accountId,
                'debit' => number_format($debit, 2, '.', ''),
                'credit' => number_format($credit, 2, '.', ''),
                'narration' => $narration,
            ];
        }

        if (count($normalized) < 2) {
            return back()->withInput()->withErrors(['lines' => 'Please provide at least two non-empty lines.']);
        }

        if (number_format($totalDebit, 2, '.', '') !== number_format($totalCredit, 2, '.', '')) {
            return back()->withInput()->withErrors(['balance' => 'Journal entry must be balanced.']);
        }

        \DB::transaction(function () use ($journal, $request, $normalized) {
            $journal->update([
                'entry_date' => $request->entry_date,
                'reference' => $request->reference,
                'description' => $request->description,
            ]);

            $journal->lines()->delete();
            foreach ($normalized as $ln) {
                $journal->lines()->create($ln);
            }
        });

        return redirect()->route('journal.index')->with('success', 'Journal updated.');
    }

    /**
     * Remove the specified journal entry (and lines).
     */
    public function destroy(JournalEntry $journal)
    {
        $journal->delete();
        return back()->with('success','Journal entry deleted.');
    }
}
