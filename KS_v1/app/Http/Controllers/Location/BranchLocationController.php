<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\BranchLocation;
use App\Models\Branch;
use App\Http\Requests\Branches\LocationRequest;

class BranchLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grouped = BranchLocation::with('branch.company')
            ->orderByDesc('id')
            ->get()
            ->groupBy('location_name')
            ->map(fn ($group) => $group->first()->setAttribute('_branches', $group->pluck('branch')->filter()->values()))
            ->values();

        $page = Paginator::resolveCurrentPage();
        $perPage = 50;
        $total = $grouped->count();

        $locations = new LengthAwarePaginator(
            $grouped->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('branchlocations.index', compact('locations'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $branches = Branch::visibleToUser(auth()->user())->active()->get();

        return view('branchlocations.create',compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_name'=>'required',
            'branch_id'=>'required|array',
            'branch_id.*'=>'required|exists:branches,id'
        ]);

        if(is_array($request->branch_id) && !empty($request->branch_id)){

            foreach($request->branch_id as $branchId){

                $area=BranchLocation::Where('location_name',$request->location_name)->Where('branch_id',$branchId)->first();

                if(!$area){

                    $area=new BranchLocation();

                    $area->location_name=$request->location_name;

                    $area->branch_id=$branchId;

                }

                $area->save();


            }


        }

        return redirect()
            ->route('location.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BranchLocation $location)
    {
         $area = $location;
    $branches = Branch::visibleToUser(auth()->user())->active()->get();

    // Load ALL branch IDs for this area name so edit form pre-selects all of them
    $selectedBranchIds = BranchLocation::where('location_name', $location->location_name)
        ->pluck('branch_id')
        ->toArray();

    $location->setAttribute('_selected_branch_ids', $selectedBranchIds);

    return view('branchlocations.edit', compact('location', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request,BranchLocation $location)
    {
        $validated = $request->validated();

        // Web form submits branch_id as branch_id[]; normalize to a flat integer list.
        $branchIdsInput = $request->input('branch_id', []);
        $branchIds = is_array($branchIdsInput) ? $branchIdsInput : [$branchIdsInput];
        $branchIds = array_values(array_unique(array_map('intval', array_filter($branchIds))));

        // Fallback to current branch when request carries only a scalar via validation.
        if (empty($branchIds) && isset($validated['branch_id'])) {
            $branchIds = [(int) $validated['branch_id']];
        }

        $oldLocationName = $location->location_name;
        $newLocationName = $validated['location_name'] ?? $oldLocationName;
        $newStatus = $validated['status'] ?? $location->status;

        $existingRows = BranchLocation::where('location_name', $oldLocationName)
            ->get()
            ->keyBy('branch_id');

        foreach ($branchIds as $branchId) {
            $row = $existingRows->get($branchId);

            if ($row) {
                $row->update([
                    'location_name' => $newLocationName,
                    'status' => $newStatus,
                ]);
                continue;
            }

            BranchLocation::create([
                'location_name' => $newLocationName,
                'branch_id' => $branchId,
                'status' => $newStatus,
            ]);
        }

        if (!empty($branchIds)) {
            BranchLocation::where('location_name', $oldLocationName)
                ->whereNotIn('branch_id', $branchIds)
                ->delete();
        }

        return redirect()
            ->route('location.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BranchLocation $location)
    {
        if ($location->hasDependencies()) {
            return back()->with('error', 'cannot delete location because it has mapped with other records');
        }
    
        // Delete all rows sharing the same location_name (all branches for this area)
        BranchLocation::where('location_name', $location->location_name)->delete();
    
        return redirect()
            ->route('location.index')
            ->with('success', 'Location deleted successfully.');
    
    }

     public function updateStatus(Request $request)
    {
        $location = BranchLocation::findOrFail($request->id);

        $location->status = $request->status;
        $location->save();

        return response()->json(['success' => true]);
    }
}
