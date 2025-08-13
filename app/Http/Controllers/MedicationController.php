<?php
// app/Http/Controllers/MedicationController.php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationController extends Controller
{
    /**
     * Display a listing of the medications with optional search and category filters.
     *
     * @param Request $request The HTTP request containing optional 'search' and 'category' parameters.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Medication::query();

        // Search by name, generic name, or barcode/NDC
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('barcode_or_ndc', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Paginate results and keep query parameters for filters
        $medications = $query->orderBy('name')
                             ->paginate(10)
                             ->appends($request->only(['search','category']));

        // Get list of all categories for the filter dropdown
        $categories = Medication::select('category')
                         ->distinct()
                         ->orderBy('category')
                         ->pluck('category');

        return view('medications.index', compact('medications', 'categories'));
    }

    /**
     * Show the form for creating a new medication.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $unitStrengths   = ['mg','g','mcg','IU','ml','unit','%'];
        $categoriesList  = ['Tablet','Capsule','Syrup','Injection','Cream','Drops','Patch','Spray','Suppository','Inhaler','Others'];
        $dispensingUnits = ['Tablet','Capsule','ml','sachet','vial','puff','drop','unit'];

        return view('medications.create', compact('unitStrengths','categoriesList','dispensingUnits'));
    }

    /**
     * Store a newly created medication in the database.
     *
     * @param Request $request The HTTP request containing medication details.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|unique:medications,name',
            'generic_name'          => 'nullable|string',
            'strength'              => 'required|numeric',
            'unit_of_strength'      => 'required|in:mg,g,mcg,IU,ml,unit,%',
            'category'              => 'required|in:Tablet,Capsule,Syrup,Injection,Cream,Drops,Patch,Spray,Suppository,Inhaler,Others',
            'dispensing_unit'       => 'required|in:Tablet,Capsule,ml,sachet,vial,puff,drop,unit',
            'pack_size'             => 'required|integer|min:1',
            'quantity'              => 'required|integer|min:0',
            'reorder_level'         => 'nullable|integer|min:0',
            'manufacturer'          => 'nullable|string',
            'barcode_or_ndc'        => 'nullable|string|unique:medications,barcode_or_ndc',
            'description'           => 'nullable|string',
            'is_controlled'         => 'boolean',
            'requires_refrigeration'=> 'boolean',
            'storage_conditions'    => 'nullable|string',
        ]);

        // Create new medication and attach the current logged-in user as the creator
        Medication::create(array_merge(
            $request->only([
                'name','generic_name','strength','unit_of_strength',
                'category','dispensing_unit','pack_size','quantity','reorder_level','manufacturer',
                'barcode_or_ndc','description','is_controlled',
                'requires_refrigeration','storage_conditions'
            ]),
            ['created_by' => Auth::id()]
        ));

        return redirect()->route('medications.index')
                         ->with('success', 'Medication added successfully.');
    }

    /**
     * Show the form for editing an existing medication.
     *
     * @param Medication $medication The medication instance to edit.
     * @return \Illuminate\View\View
     */
    public function edit(Medication $medication)
    {
        $unitStrengths   = ['mg','g','mcg','IU','ml','unit','%'];
        $categoriesList  = ['Tablet','Capsule','Syrup','Injection','Cream','Drops','Patch','Spray','Suppository','Inhaler','Others'];
        $dispensingUnits = ['Tablet','Capsule','ml','sachet','vial','puff','drop','unit'];

        return view('medications.edit', compact('medication','unitStrengths','categoriesList','dispensingUnits'));
    }

    /**
     * Update an existing medication in the database.
     *
     * @param Request $request The HTTP request containing updated medication details.
     * @param Medication $medication The medication instance being updated.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Medication $medication)
    {
        $request->validate([
            'name'                  => 'required|string|unique:medications,name,' . $medication->id,
            'generic_name'          => 'nullable|string',
            'strength'              => 'required|numeric',
            'unit_of_strength'      => 'required|in:mg,g,mcg,IU,ml,unit,%',
            'category'              => 'required|in:Tablet,Capsule,Syrup,Injection,Cream,Drops,Patch,Spray,Suppository,Inhaler,Others',
            'dispensing_unit'       => 'required|in:Tablet,Capsule,ml,sachet,vial,puff,drop,unit',
            'pack_size'             => 'required|integer|min:1',
            'quantity'              => 'required|integer|min:0',
            'reorder_level'         => 'nullable|integer|min:0',
            'manufacturer'          => 'nullable|string',
            'barcode_or_ndc'        => 'nullable|string|unique:medications,barcode_or_ndc,' . $medication->id,
            'description'           => 'nullable|string',
            'is_controlled'         => 'boolean',
            'requires_refrigeration'=> 'boolean',
            'storage_conditions'    => 'nullable|string',
        ]);

        // Update medication record
        $medication->update($request->only([
            'name','generic_name','strength','unit_of_strength',
            'category','dispensing_unit','pack_size','quantity','reorder_level','manufacturer',
            'barcode_or_ndc','description','is_controlled',
            'requires_refrigeration','storage_conditions'
        ]));

        return redirect()->route('medications.index')
                         ->with('success', 'Medication updated successfully.');
    }

    /**
     * Remove a medication from the database.
     *
     * @param Medication $medication The medication instance to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Medication $medication)
    {
        $medication->delete();
        return back()->with('success', 'Medication deleted successfully.');
    }

    /**
     * Show the restock form for a specific medication.
     *
     * @param Medication $medication The medication instance to restock.
     * @return \Illuminate\View\View
     */
    public function restockForm(Medication $medication)
    {
        return view('medications.restock', compact('medication'));
    }

    /**
     * Increase the stock quantity of a medication.
     *
     * @param Request $request The HTTP request containing the 'quantity' to add.
     * @param Medication $medication The medication instance to restock.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restock(Request $request, Medication $medication)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        // Increment the medication stock
        $medication->increment('quantity', $request->quantity);

        return redirect()->route('medications.index')->with('success', 'Stock updated.');
    }
}
