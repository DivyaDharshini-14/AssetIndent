<?php

namespace App\Http\Controllers\Transmission;

use App\Http\Controllers\Controller;
use App\Models\Transmission;
use Illuminate\Http\Request;

class TransmissionController extends Controller
{
    // TransmissionController@index
    public function index(Request $request) {
      $transmissions = Transmission::orderBy('name')->paginate(15);
      $activeTransmissions = Transmission::active()->count();
      $inactiveTransmissions = Transmission::inactive()->count();
      return view('transmissions.index', compact('transmissions', 'activeTransmissions', 'inactiveTransmissions'));
    }

    // create
    public function create() {
      return view('transmissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:transmissions',
            'status' => 'required|in:active,inactive',
        ]);

        Transmission::create($validated);

        return redirect()->route('transmissions.index')
            ->with('success', 'Transmission created successfully.');
    }

    public function show(Transmission $transmission)
    {
        return view('transmissions.show', compact('transmission'));
    }

    public function edit(Transmission $transmission)
    {
        return view('transmissions.edit', compact('transmission'));
    }

    public function update(Request $request, Transmission $transmission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:transmissions,name,' . $transmission->id,
            'status' => 'required|in:active,inactive',
        ]);

        $transmission->update($validated);

        return redirect()->route('transmissions.index')
            ->with('success', 'Transmission updated successfully.');
    }

    public function destroy(Transmission $transmission)
    {
        if ($transmission->vehicleModels()->count() > 0) {
            return back()->with('error', 'Cannot delete transmission used in vehicle models.');
        }

        $transmission->delete();

        return redirect()->route('transmissions.index')
            ->with('success', 'Transmission deleted successfully.');
    }
}
