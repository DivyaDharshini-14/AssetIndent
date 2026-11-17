<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Branch;
use App\Models\BranchLocation;
use App\Models\AssetAssignment;
use App\AssetStatus;
use App\AssetCondition;
use App\Http\Requests\Assets\AssetRequest;
use App\Models\Vendor;
use App\Models\AssetCategory;
use App\Helpers\Files;
use App\Models\Brand; 
use App\Models\Company;
use App\AssetOperationalStatus;
use App\Models\AssetImport as AssetImportModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;


class AssetController extends Controller
{
    private const ASSET_PER_PAGE = 50;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            app(\App\Services\Procurement\GrnService::class)
                ->syncApprovedGrnAssets(auth()->user());
        } catch (\Throwable) {
            // Never block asset listing if GRN sync fails.
        }

        $assetTypes=AssetType::active()->get();
        $subCategories = AssetCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        $statuses = AssetStatus::casesForFilter();

        $conditions=AssetCondition::cases();      

        $vendors=Vendor::active()->get();

        $companies=Company::visibleToUser(auth()->user())->active()->get(); 

           $branches = Branch::visibleToUser(auth()->user())
            ->where('status', 'active')
            ->select('id', 'name', 'company_id')
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->get();


        $assets=Asset::query()

    ->VisibleToUser(auth()->user())

    ->with([
        'latestAssignment.fromBranch',
        'latestAssignment.toBranch',
        'latestAssignment.fromCompany',
        'latestAssignment.toCompany',
        'latestAssignment.fromBranchLocation',
        'latestAssignment.toBranchLocation',
        'latestAssignment.employee',
        'assetType',
        'branch',
        'location',
        'subCategory',
    ])

    ->when($request->company_id, function ($q) use ($request) {
        $q->where(function ($inner) use ($request) {
            $inner->where('company_id', $request->company_id)
                // GRN assets may briefly have null company when warehouse settings are stale
                ->orWhere(function ($grnAssets) {
                    $grnAssets->whereNull('company_id')->whereNotNull('grn_item_id');
                });
        });
    })

    ->when($request->branch_id, function ($q) use ($request) {
        $q->filteredByBranch((int) $request->branch_id);
    })

    ->when($request->asset_type_id, function ($q) use ($request) {
        $q->where('asset_type_id', $request->asset_type_id);
    })

    ->when($request->sub_category_id, function ($q) use ($request) {
        $q->where('sub_category_id', $request->sub_category_id);
    })

    ->when($request->status, function ($q) use ($request) {
        $status = (string) $request->status;
        if ($status === 'under_maintenance') {
            $q->where('operational_status', 'under_maintenance');
            return;
        }

        if ($status === AssetStatus::TRANSFERRED->value) {
            $assetsTable = $q->getModel()->getTable();
            $assignmentsTable = (new AssetAssignment)->getTable();

            $q->where(function (Builder $w) use ($assetsTable, $assignmentsTable): void {
                $w->whereIn($assetsTable . '.status', ['transferred', 'in_transit'])
                    ->orWhereExists(function ($sub) use ($assetsTable, $assignmentsTable): void {
                        $sub->selectRaw('1')
                            ->from($assignmentsTable)
                            ->whereColumn($assignmentsTable . '.asset_id', $assetsTable . '.id')
                            ->where($assignmentsTable . '.transfer_status', AssetAssignment::IN_TRANSIT)
                            ->whereRaw($assignmentsTable . '.id = (
                                SELECT MAX(aa_inner.id)
                                FROM ' . $assignmentsTable . ' AS aa_inner
                                WHERE aa_inner.asset_id = ' . $assetsTable . '.id
                            )');
                    });
            });

            return;
        }

        $q->where('status', $status);
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
        $termNormalized = strtolower(preg_replace('/\s+/', '', $term));

        $q->where(function (Builder $w) use ($term, $termNormalized): void {
            $w->where('asset_code', 'like', "%{$term}%")
                ->orWhere('asset_name', 'like', "%{$term}%")
                ->orWhere('serial_no', 'like', "%{$term}%")
                // Serial match fallback: ignore case + spaces.
                ->orWhereRaw('LOWER(REPLACE(COALESCE(serial_no, ""), " ", "")) LIKE ?', ["%{$termNormalized}%"]);
        });
    })

     

    ->orderBy('id', 'desc')
    ->paginate(self::ASSET_PER_PAGE)
    ->withQueryString();

        return view('assets.index',compact('assetTypes','subCategories','assets','conditions','statuses','companies','branches'));    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assetTypes=AssetType::active()->get();

        $categories=AssetCategory::active()->WhereNULL('parent_id')->OrderBy('id','DESC')->get();

        $statuses = AssetStatus::casesForFilter();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $companies=Company::visibleToUser(auth()->user())->active()->get(); 

         $vendors=Vendor::active()->get();

         $brands=Brand::active()->get();

        $duplicateSavedMessage = session()->pull('asset_duplicate_saved');
        $isDuplicateReturn = $duplicateSavedMessage !== null;
        $repopulateForm = $isDuplicateReturn || session()->has('errors');

        if (! $repopulateForm) {
            session()->forget('_old_input');
        }

        $assetCode = $isDuplicateReturn
            ? session()->pull('nextAssetCode', Asset::generateAssetCode())
            : Asset::generateAssetCode();

        $warehouseCompanyId = (int) setting('warehouse_company_id', 0);
        $warehouseBranchId = (int) setting('warehouse_id', 0);
        $warehouseLocationId = setting('warehouse_location');

        return view('assets.create', compact(
            'operations_status',
            'categories',
            'statuses',
            'conditions',
            'vendors',
            'companies',
            'brands',
            'assetCode',
            'warehouseCompanyId',
            'warehouseBranchId',
            'warehouseLocationId',
            'isDuplicateReturn',
            'duplicateSavedMessage',
            'repopulateForm'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssetRequest $request)
    {

        $data = $request->validated();

        $statusValue = $data['status'] instanceof AssetStatus
            ? $data['status']->value
            : (string) $data['status'];

        $assignmentPayload = null;

        if ($statusValue === AssetStatus::ASSIGNED->value) {
            $assignmentPayload = [
                'assign_date' => now()->toDateString(),
                'from_company_id' => (int) $data['company_id'],
                'from_branch' => (int) $data['branch_id'],
                'from_location' => $data['location_id'] ?? null,
                'to_company_id' => (int) $data['to_company_id'],
                'to_branch' => (int) $data['to_branch'],
                'to_location' => $data['to_location'] ?? null,
                'transfer_status' => AssetAssignment::IN_TRANSIT,
            ];

            unset($data['to_company_id'], $data['to_branch'], $data['to_location']);
            $data['status'] = AssetStatus::AVAILABLE->value;
        }

        if ($request->hasFile('filename') && $request->file('filename')->isValid()) {

            $data['filename'] = Files::uploadLocalOrS3($request->filename, Asset::FILE_PATH);
        }

        $asset = Asset::create($data);

        if ($assignmentPayload !== null) {
            $asset->assignments()->create($assignmentPayload);
        }

        if ($request->has('save_and_duplicate')) {
            $flashInput = $request->except([
                '_token',
                'filename',
                'save_and_duplicate',
                'asset_code',
            ]);
            $flashInput['status'] = (string) $request->input('status', $statusValue);

            return redirect()
                ->route('asset.create')
                ->withInput($flashInput)
                ->with('asset_duplicate_saved', 'Asset saved. Form kept as-is — only asset code was updated.')
                ->with('nextAssetCode', Asset::generateAssetCode());
        }

        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset,Request $request)
    {

        $asset->loadMissing([
            'latestAssignment.fromCompany',
            'latestAssignment.toCompany',
            'latestAssignment.fromBranch',
            'latestAssignment.toBranch',
            'latestAssignment.fromBranchLocation',
            'latestAssignment.toBranchLocation',
            'grnItem.grn',
            'grnItem.poItem',
            'purchaseOrder.purchaseRequisition.indent.department',
            'purchaseOrder.vendor',
            'indent.department',
            'department',
            'vendor',
            'brand',
            'category',
            'subCategory',
            'assetType',
            'company',
            'branch',
            'location',
        ]);

        $tab=$request->tab ?? 'assignments';

        $scrollToTabSection = session()->pull('scroll_asset_section');

        $assignments = AssetAssignment::visibleToUser(auth()->user())
            ->with([
                'asset',
                'fromCompany',
                'toCompany',
                'fromBranch',
                'toBranch',
                'fromBranchLocation',
                'toBranchLocation',
                'employee.designation',
            ])
            ->Where('asset_id', $asset->id)
            ->latest()
            ->get();

        return view('assets.show',compact('asset','tab','assignments','scrollToTabSection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        if (! $asset->isEditable()) {
            return redirect()
                ->route('asset.show', $asset)
                ->with('error', 'This asset cannot be edited once it has been assigned.');
        }

        $assetTypes=AssetType::active()->get();

        $statuses = AssetStatus::casesForFilter();

        $conditions=AssetCondition::cases();

        $companies=Company::visibleToUser(auth()->user())->active()->get(); 

        $branches=Branch::visibleToUser(auth()->user())->Where('company_id',$asset->company_id)->active()->get();

        $locations=BranchLocation::Where('branch_id', $asset->branch_id)->where('status', 'active')->get();

            $operations_status=AssetOperationalStatus::cases();

        $vendors=Vendor::active()->get();

          $categories=AssetCategory::active()->WhereNULL('parent_id')->OrderBy('id','DESC')->get();

           $brands=Brand::active()->get();

        return view('assets.edit',compact('operations_status','companies','categories','asset','statuses','conditions','vendors','branches','locations','brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetRequest $request, Asset $asset)
    {
        if (! $asset->isEditable()) {
            return redirect()
                ->route('asset.show', $asset)
                ->with('error', 'This asset cannot be edited once it has been assigned.');
        }

         $data=$request->validated();

         if($request->hasFile('filename') && $request->file('filename')->isValid()){    

            $data['filename'] = Files::uploadLocalOrS3($request->filename,  Asset::FILE_PATH);
        }   

        $asset->update($data);

        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset updated successfully.');
         
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {

        if ($asset->hasDependencies()) {

            return redirect()
            ->route('asset.index')
            ->with('error', 'Asset has dependencies and cannot be deleted.');
        }

        $asset->delete();
        return redirect()
            ->route('asset.index')
            ->with('success', 'Asset deleted successfully.');
    }

    public function depreciation(Request $request){


        $branches=Branch::visibleToUser(auth()->user())->active()->get(); 

         // Get asset types for filter dropdown
        $assetTypes = AssetType::where('status', 'active')->get();

        // Sub-categories labelled "Category" in the UI (child rows under parent category)
        $categories = AssetCategory::query()
            ->whereNotNull('parent_id')
            ->where('status', 'active')
            ->orderBy('category_name')
            ->get();

        // Build query
        $assets = Asset::query()
            ->when($request->branch_id, fn ($q) => $q->filteredByBranch((int) $request->branch_id))
            ->when($request->filled('sub_category_id'), fn($q) => $q->where('sub_category_id', $request->sub_category_id))
            ->when($request->asset_type_id, fn($q) => $q->where('asset_type_id', $request->asset_type_id))
            ->when($request->filled('search'), function ($q) use ($request): void {
                $term = trim((string) $request->search);
                if ($term === '') {
                    return;
                }

                $q->where(function ($w) use ($term): void {
                    $w->where('asset_code', 'like', "%{$term}%")
                        ->orWhere('asset_name', 'like', "%{$term}%");
                });
            })
            ->when($request->depreciation_method, fn($q) => $q->where('depreciation_method', $request->depreciation_method))
            ->when($request->from, fn($q) => $q->whereDate('purchase_date', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('purchase_date', '<=', $request->to))
            ->when(
                ! $request->branch_id || ! Asset::isWarehouseStoreBranch((int) $request->branch_id),
                fn ($q) => $q->where('status', 'assigned')
            )
            ->with(['assetType', 'branch', 'location', 'subCategory', 'latestAssignment.employee'])
            ->latest()
            ->paginate(self::ASSET_PER_PAGE)
            ->withQueryString();

        return view('assets.depreciation', compact('assetTypes', 'assets', 'branches', 'categories'));


    }


    public function import(){

        return view('assets.import');
    }

    public function importAsset(Request $request){

        $request->validate([
        'assetfile' => 'required|mimes:xlsx,csv'
         ]);

         AssetImportModel::truncate();

         Excel::import(new AssetImport, $request->file('assetfile'));

         return redirect()->route('asset.import.preview')->with('success', 'Asset imported successfully.');

         
    }

    public function importPreview(){

        $imports=AssetImportModel::WhereNULL('processed_at')->paginate(50);

        $validAsset=AssetImportModel::WhereNULL('processed_at')->where('is_valid', 1)->count();

        $invalidAsset=AssetImportModel::WhereNULL('processed_at')->where('is_valid', 0)->count();

        return view('assets.importpreview',compact('imports','validAsset','invalidAsset'));
    }


    public function moveValidData(){

         DB::beginTransaction();

        $wareHouseCompanyId=setting('warehouse_company_id',1);

        $wareHouseId=setting('warehouse_id',1);

        $wareHouseLocationId=setting('warehouse_location',1);

        
           try {

             $imports = AssetImportModel::where('is_valid', 1)
                ->whereNull('processed_at')
                ->get();

            if ($imports->isEmpty()) {
                return back()->with('error', 'No valid data to import');
            }

            // Existing serials
            $existingSerials = array_flip(
                Asset::pluck('serial_no')
                    ->map(fn($s) => strtolower(trim($s)))
                    ->toArray()
            );


            $insertData=[];

            // Existing asset codes
            $existingAssetCodes = array_flip(
                Asset::pluck('asset_code')
                    ->map(fn($s) => strtolower(trim($s)))
                    ->toArray()
            );

            foreach ($imports as $import) {

                $data = json_decode($import->data, true);

                if (!$data) {
                    continue;
                }

                $serial = strtolower(trim($data['serial_no'] ?? ''));
                $assetCode = trim($data['asset_code'] ?? '');

                // Skip duplicate serial or missing/duplicate asset code
                if (!$serial || isset($existingSerials[$serial]) || !$assetCode || isset($existingAssetCodes[strtolower($assetCode)])) {
                    continue;
                }

                // Add to tracking to prevent duplicates within this batch
                $existingAssetCodes[strtolower($assetCode)] = true;

                $insertData[] = [
                   'asset_code' => $assetCode,
                   'asset_name' => $data['asset_name'] ?? '',
                   'serial_no' => $data['serial_no'] ?? '',
                   'asset_structure' => $data['asset_structure'] ?? '',
                   'items_count' => $data['items_count'] ?? '',
                   'category_id' => $data['category_id'] ?? '',
                   'sub_category_id' => $data['sub_category_id'] ?? '',
                   'asset_type_id' => $data['asset_type_id'] ?? '',
                   'operational_status' => $data['operational_status'] ?? '',  
                   'condition' => $data['condition'] ?? '',  
                   'status' => 'available',
                   'company_id'=> $wareHouseCompanyId,
                   'branch_id'=> $wareHouseId,
                   'location_id'=> $wareHouseLocationId,                   
                   'purchase_date' => $data['purchase_date'] ?? '', 
                   'purchase_cost' => $data['purchase_cost'] ?? '',       
                   'supplier' => $data['vendor_id'] ?? '',  
                   'brand_id' => $data['brand_id'] ?? NULL,  
                   'invoice_details' => $data['invoice_details_original'] ?? '',  
                   'depreciation_method' => $data['depreciation_method'] ?? '',  
                   'salvage_value' => $data['depreciation_rate'] ?? 0,   
                   'description' => $data['description'] ?? '',  
                   'created_by' => auth()->user()->id
                ];

                $import->processed_at = now();
                $import->save();

            }


             foreach (array_chunk($insertData, 500) as $chunk) {

                 Asset::insert($chunk);
             }

           

            DB::commit();

             return redirect()->route('asset.index')->with('success', 'Asset imported successfully.');

           } catch (\Throwable $e) {
               DB::rollBack();

               return back()->with('error', $e->getMessage());
           }

       
    }


    public function generateAssetCode(){

        Asset::whereNull('asset_code')
            ->orderBy('id')
            ->chunkById(1000, function ($assets) {

                if ($assets->isEmpty()) {
                    return;
                }

                foreach ($assets as $asset) {

                    $assetCode=Asset::generateAssetCode();

                    while (Asset::where('asset_code', $assetCode)->exists()) {
                        $assetCode = Asset::generateAssetCode();
                    }

                    $prefix = Asset::ASSET_PREFIX;
                    $asset->asset_code = $assetCode;
                    $asset->asset_prefix = $prefix;
                    $asset->asset_nos = preg_match('/(\d+)$/', $assetCode, $matches)
                        ? (int) $matches[1]
                        : null;
                    $asset->save();
                }

            });
        
    }




}
