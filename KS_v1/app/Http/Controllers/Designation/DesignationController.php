<?php
namespace App\Http\Controllers\Designation;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Requests\Designation\DesignationRequest;
class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::orderByDesc('id')->paginate(50);

        return view('designation.index', compact('designations'));
    }

    public function create()
    {
        return view('designation.create');
    }

    public function edit(Designation $designation)
    {
        return view('designation.edit', compact('designation'));
    }

    public function store(DesignationRequest $request)
    {
        $validated = $request->validated();

        Designation::create($validated);

        return redirect()
            ->route('designation.index')
            ->with('success', 'Designation created successfully.');
    }

    public function update(DesignationRequest $request, Designation $designation)
    {
        $validated = $request->validated();

        $designation->update($validated);

        return redirect()
            ->route('designation.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        return redirect()
            ->route('designation.index')
            ->with('success', 'Designation deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $designation = Designation::findOrFail($request->id);

        $designation->status = $request->status;
        $designation->save();

        return response()->json(['success' => true]);
    }

}
