<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Traits\ApiResponse;
use App\Http\Requests\Assets\AssetRequest;
use App\AssetStatus;
use App\AssetCondition;
use App\AssetOperationalStatus;
use App\Helpers\Files;
use Illuminate\Database\Eloquent\Builder;

class AssetController extends Controller
{

    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $assets=Asset::with([
        'vendor:id,name',
        'brand:id,brand_name',
        'branch:id,name',
        'company:id,name',
        'assetType:id,asset_type_name',
        'category:id,category_name',
        'subCategory:id,category_name',
        'location:id,location_name',
        'createdBy:id,name'
    ])
    ->visibleToUser(auth()->user())

    ->when($request->company_id, function ($q) use ($request) {
        $q->where('company_id', $request->company_id);
    })

    ->when($request->branch_id, function ($q) use ($request) {
        $q->filteredByBranch((int) $request->branch_id);
    })

    ->when($request->asset_type_id, function ($q) use ($request) {
        $q->where('asset_type_id', $request->asset_type_id);
    })

    ->when($request->status, function ($q) use ($request) {
        $q->where('status', $request->status);
    })

    ->when($request->condition, function ($q) use ($request) {
        $q->where('condition', $request->condition);
    })

    ->when($request->purchase_date_from, function ($q) use ($request) {
        $q->whereDate('purchase_date', '>=', $request->purchase_date_from);
    })

    ->when($request->purchase_date_to, function ($q) use ($request) {
        $q->whereDate('purchase_date', '<=', $request->purchase_date_to);
    })

    ->when($request->filled('search'), function ($q) use ($request) {
        $term = trim((string) $request->search);
        if ($term === '') {
            return;
        }

        $q->where(function (Builder $w) use ($term): void {
            $w->where('asset_code', 'like', "%{$term}%")
                ->orWhere('asset_name', 'like', "%{$term}%")
                ->orWhere('serial_no', 'like', "%{$term}%");
        });
    })

    ->latest()   // same as orderBy('id','DESC')
    ->paginate(50)
    ->withQueryString();

        return $this->successWithPagination($assets);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssetRequest $request)
    {
        $data=$request->validated();

         $wareHouseCompanyId=setting('warehouse_company_id',1);

        $wareHouseId=setting('warehouse_id',1);

        $wareHouseLocationId=setting('warehouse_location',1);

        if($data['status'] == 'assigned' && $data['company_id'] == $wareHouseCompanyId && $data['branch_id'] == $wareHouseId){

            return redirect()->back()->with('error', 'Asset cannot be assigned to warehouse');

        }         

        if($request->hasFile('filename') && $request->file('filename')->isValid()){    

            $data['filename'] = Files::uploadLocalOrS3($request->filename,  Asset::FILE_PATH);
        }    

        $asset=Asset::create($data);

        if($data['status'] == 'assigned'){

            $asset->assignments()->create([
                'asset_id'=>$asset->id,
                'assign_date'=>date('Y-m-d'),                
                'from_branch' => $wareHouseId,
                'from_location' => $wareHouseLocationId,
                'to_branch' => $data['branch_id'],
                'to_location' => $data['location_id'],
                'transfer_status' => 'in_transit',
            ]);

        }

        return $this->success($asset);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset= Asset::find($id);

        if(!$asset){ 
         
            return $this->error('Asset not found');
        }

        return $this->success($asset);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetRequest $request, string $id)
    {
        $asset= Asset::find($id);

        if(!$asset){ 
         
            return $this->error('Asset not found');
        }

        $asset->update($request->validated());

        return $this->success($asset);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset= Asset::find($id);

        if(!$asset){ 
         
            return $this->error('Asset not found');
        }

        $asset->delete();

        return $this->success('Asset deleted successfully');
    }

    public function status(){

        $statuses=AssetStatus::cases();

        return $this->success($statuses);

    }

    public function condition(){

        $conditions=AssetCondition::cases();

        return $this->success($conditions);
    }

    public function getnextassetcode(){

        $assetCode=Asset::generateAssetCode();

        return $this->success($assetCode);

    }

    public function operatingStatus(){

        $operationalStatus=AssetOperationalStatus::cases();

        return $this->success($operationalStatus);

    }
}
