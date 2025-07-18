<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource, with search & category filter.
     */
    public function index(Request $request)
    {
        $query = Medication::query();

        // Search by name, generic_name, or barcode/ndc
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

        $medications = $query->orderBy('name')
                             ->paginate(10)
                             ->appends($request->only(['search','category']));

        $categories = Medication::select('category')
                         ->distinct()
                         ->orderBy('category')
                         ->pluck('category');

        return view('medications.index', compact('medications', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitStrengths   = ['mg','g','mcg','IU','ml','unit','%'];
        $categoriesList  = ['Tablet','Capsule','Syrup','Injection','Cream','Drops','Patch','Spray','Suppository','Inhaler','Others'];
        $dispensingUnits = ['Tablet','Capsule','ml','sachet','vial','puff','drop','unit'];

        return view('medications.create', compact('unitStrengths','categoriesList','dispensingUnits'));
    }

    /**
     * Store a newly created resource in storage.
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
     * Show the form for editing the specified resource.
     */
    public function edit(Medication $medication)
    {
        $unitStrengths   = ['mg','g','mcg','IU','ml','unit','%'];
        $categoriesList  = ['Tablet','Capsule','Syrup','Injection','Cream','Drops','Patch','Spray','Suppository','Inhaler','Others'];
        $dispensingUnits = ['Tablet','Capsule','ml','sachet','vial','puff','drop','unit'];

        return view('medications.edit', compact('medication','unitStrengths','categoriesList','dispensingUnits'));
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        $medication->delete();
        return back()->with('success', 'Medication deleted successfully.');
    }

    /**
     * Show the restock form for a specific medication.
     */
    public function restockForm(Medication $medication)
    {
        return view('medications.restock', compact('medication'));
    }

    /**
     * Process a stock increment for a medication.
     */
    public function restock(Request $request, Medication $medication)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $medication->increment('quantity', $request->quantity);
        return redirect()->route('medications.index')->with('success', 'Stock updated.');
    }
}
