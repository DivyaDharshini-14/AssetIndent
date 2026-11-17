<?php

namespace App\Http\Controllers\VehicleModel;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use App\Models\Segment;
use App\Models\Transmission;
use App\Models\FuelType;
use App\Models\Company;
use App\Models\DealerLocation;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    public function index(Request $request) {
      $models = VehicleModel::with(['segment', 'transmission', 'fuelType', 'company', 'dealerLocation'])
          ->when($request->name, fn($q) => $q->where('name', 'like', '%'.$request->name.'%'))
          ->when($request->segment_id, fn($q) => $q->where('segment_id', $request->segment_id))
          ->when($request->fuel_type_id, fn($q) => $q->where('fuel_type_id', $request->fuel_type_id))
          ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
          ->orderBy('name')
          ->paginate(10);

      $activeModels = VehicleModel::active()->count();
      $inactiveModels = VehicleModel::inactive()->count();

      $segments = Segment::active()->get();
      $fuelTypes = FuelType::active()->get();
      $companies = Company::active()->get();

      return view('vehicle-models.index', compact('models', 'activeModels', 'inactiveModels', 'segments', 'fuelTypes', 'companies'));
    }

    // create
    public function create() {
        $segments = Segment::active()->get();
        $transmissions = Transmission::active()->get();
        $fuelTypes = FuelType::active()->get();
        $dealerLocations = DealerLocation::active()->get();
        $companies = Company::active()->get();

        return view('vehicle-models.create', compact('segments', 'transmissions', 'fuelTypes', 'dealerLocations', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'segment_id' => 'required|exists:segments,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'dealer_location_id' => 'required|exists:dealer_locations,id',
            'company_id' => 'required|exists:companies,id',
            'status' => 'required|in:active,inactive',
        ]);

        VehicleModel::create($validated);

        return redirect()->route('vehicle-models.index')
            ->with('success', 'Vehicle model created successfully.');
    }

    public function show(VehicleModel $vehicleModel)
    {
        $vehicleModel->load(['company', 'dealerLocation', 'segment', 'transmission', 'fuelType']);
        return view('vehicle-models.show', compact('vehicleModel'));
    }

    public function edit(VehicleModel $vehicleModel)
    {
        $segments = Segment::active()->get();
        $transmissions = Transmission::active()->get();
        $fuelTypes = FuelType::active()->get();
        $companies = Company::active()->get();
        $dealerLocations = DealerLocation::active()->get();

        return view('vehicle-models.edit', compact(
            'vehicleModel', 'segments', 'transmissions',
            'fuelTypes', 'companies', 'dealerLocations'
        ));
    }

    public function update(Request $request, VehicleModel $vehicleModel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'segment_id' => 'required|exists:segments,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'dealer_location_id' => 'required|exists:dealer_locations,id',
            'company_id' => 'required|exists:companies,id',
            'status' => 'required|in:active,inactive',
        ]);

        $vehicleModel->update($validated);

        return redirect()->route('vehicle-models.index')
            ->with('success', 'Vehicle model updated successfully.');
    }

    public function destroy(VehicleModel $vehicleModel)
    {
        $vehicleModel->delete();

        return redirect()->route('vehicle-models.index')
            ->with('success', 'Vehicle model deleted successfully.');
    }
}
