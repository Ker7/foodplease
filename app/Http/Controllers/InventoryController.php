<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->boolean('expiring_soon')) {
            $query->expiringSoon();
        }
        
        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }
        
        $inventory = $query->latest()->get();
        
        if ($request->header('HX-Request')) {
            return view('inventory.partials.list', compact('inventory'));
        }
        
        return view('inventory.index', compact('inventory'));
    }

    public function create(Request $request)
    {
        if ($request->header('HX-Request')) {
            return view('inventory.partials.create-form');
        }
        
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:fridge,pantry,freezer',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'low_stock_threshold' => 'nullable|numeric|min:0'
        ]);

        $item = Inventory::create($validated);

        if ($request->header('HX-Request')) {
            return view('inventory.partials.item', compact('item'));
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully!');
    }

    public function show(Inventory $inventory, Request $request)
    {
        if ($request->header('HX-Request')) {
            return view('inventory.partials.show', compact('inventory'));
        }
        
        return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory, Request $request)
    {
        if ($request->header('HX-Request')) {
            return view('inventory.partials.edit-form', compact('inventory'));
        }
        
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:fridge,pantry,freezer',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'low_stock_threshold' => 'nullable|numeric|min:0'
        ]);

        $inventory->update($validated);

        if ($request->header('HX-Request')) {
            return view('inventory.partials.item', compact('inventory'));
        }

        return redirect()->route('inventory.show', $inventory)->with('success', 'Inventory item updated successfully!');
    }

    public function destroy(Inventory $inventory, Request $request)
    {
        $inventory->delete();

        if ($request->header('HX-Request')) {
            return response('', 200);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully!');
    }
}
