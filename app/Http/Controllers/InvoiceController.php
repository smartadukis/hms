<?php
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class InvoiceController
 *
 * Handles CRUD operations for invoices and their items,
 * as well as filtering and searching invoices.
 */
class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices, with optional search and filters.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Invoice::with('patient', 'issuedBy');

        // Search by patient name
        if ($search = $request->search) {
            $query->whereHas('patient', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            );
        }

        // Filter by invoice status (unpaid, paid, partial)
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter by issuer (staff ID)
        if ($issuedBy = $request->issued_by) {
            $query->where('issued_by', $issuedBy);
        }

        $invoices = $query->latest()
                          ->paginate(10)
                          ->appends($request->only(['search','status','issued_by']));

        $issuers = User::whereIn('role', ['admin','receptionist','doctor'])->get();

        return view('invoices.index', compact('invoices','issuers'));
    }

    /**
     * Show form for creating a new invoice.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        return view('invoices.create', compact('patients'));
    }

    /**
     * Store a newly created invoice in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'            => 'required|exists:patients,id',
            'items.*.description'   => 'required|string',
            'items.*.amount'        => 'required|numeric|min:0',
            'items.*.item_type'     => 'nullable|string',
        ]);

        // Calculate invoice total from items
        $total = collect($request->items)->sum('amount');

        $invoice = Invoice::create([
            'patient_id'   => $request->patient_id,
            'total_amount' => $total,
            'issued_by'    => auth()->id(),
        ]);

        // Store each invoice item
        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'amount'      => $item['amount'],
                'item_type'   => $item['item_type'] ?? null,
            ]);
        }

        return redirect()->route('invoices.index')
                         ->with('success','Invoice created.');
    }

    /**
     * Display a single invoice with its details.
     *
     * @param Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('patient','issuedBy','items');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing an existing invoice.
     *
     * @param Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $patients = Patient::orderBy('name')->get();
        return view('invoices.edit', compact('invoice','patients'));
    }

    /**
     * Update an existing invoice and its items.
     *
     * @param Request $request
     * @param Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'patient_id'            => 'required|exists:patients,id',
            'status'                => 'required|in:unpaid,paid,partial',
            'payment_method'        => 'nullable|string',
            'items.*.description'   => 'required|string',
            'items.*.amount'        => 'required|numeric|min:0',
            'items.*.item_type'     => 'nullable|string',
        ]);

        $total = collect($request->items)->sum('amount');

        $invoice->update([
            'patient_id'    => $request->patient_id,
            'status'        => $request->status,
            'payment_method'=> $request->payment_method,
            'total_amount'  => $total,
        ]);

        // Replace existing items with new set
        $invoice->items()->delete();
        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'amount'      => $item['amount'],
                'item_type'   => $item['item_type'] ?? null,
            ]);
        }

        return redirect()->route('invoices.index')
                         ->with('success','Invoice updated.');
    }

    /**
     * Delete an invoice from the database.
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success','Invoice deleted.');
    }
}
