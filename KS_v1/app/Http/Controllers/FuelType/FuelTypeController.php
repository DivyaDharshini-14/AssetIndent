<?php

namespace App\Http\Controllers\FuelType;

use App\Http\Controllers\Controller;
use App\Models\FuelType;
use Illuminate\Http\Request;

class FuelTypeController extends Controller
{
    public function index() {
      $fuelTypes = FuelType::orderBy('name')->paginate(15);
      $activeFuelTypes = FuelType::active()->count();
      $inactiveFuelTypes = FuelType::inactive()->count();
      return view('fuel-types.index', compact('fuelTypes', 'activeFuelTypes', 'inactiveFuelTypes'));
    }

    public function create()
    {
        return view('fuel-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fuel_types',
            'status' => 'required|in:active,inactive',
        ]);

        FuelType::create($validated);

        return redirect()->route('fuel-types.index')
            ->with('success', 'Fuel type created successfully.');
    }

    public function show(FuelType $fuelType)
    {
        return view('fuel-types.show', compact('fuelType'));
    }

    public function edit(FuelType $fuelType)
    {
        return view('fuel-types.edit', compact('fuelType'));
    }

    public function update(Request $request, FuelType $fuelType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fuel_types,name,' . $fuelType->id,
            'status' => 'required|in:active,inactive',
        ]);

        $fuelType->update($validated);

        return redirect()->route('fuel-types.index')
            ->with('success', 'Fuel type updated successfully.');
    }

    public function destroy(FuelType $fuelType)
    {
        if ($fuelType->vehicleModels()->count() > 0) {
            return back()->with('error', 'Cannot delete fuel type used in vehicle models.');
        }

        $fuelType->delete();

        return redirect()->route('fuel-types.index')
            ->with('success', 'Fuel type deleted successfully.');
    }
}
