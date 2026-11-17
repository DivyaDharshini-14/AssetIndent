<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Company;
use App\AssetCondition;
use App\AssetStatus;
use App\Models\Branch;
use App\AssetOperationalStatus;
use App\Models\Brand;
use App\Models\BranchLocation;
use App\Models\Vendor;
use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Services\AssetReportExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    private const REPORT_PER_PAGE = 50;

    public function companywiseassets(Request $request){

        $companies=Company::visibleToUser(auth()->user())->active()->select('id','name')->get();

         $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query();

        $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if($request->company_id){

            $assetqry->where('company_id', $request->company_id);

        }

        if($request->status){

            $assetqry->where('status', $request->status);

        }

        if($request->condition){

            $assetqry->where('condition', $request->condition);

        }

        if($request->operational_status){

            $assetqry->where('operational_status', $request->operational_status);

        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','company_id','branch_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Companywise Assets')) {
            return $export;
        }

        return view('reports.companywiseasset',compact('companies','assets','statuses','conditions','operations_status','tableColumns'));


    }


    public function branchwiseassets(Request $request){

        $companies=Company::visibleToUser(auth()->user())->active()->select('id','name')->get();

        $branches=collect();

        $locations=collect();

        $vendors=Vendor::active()->orderBy('name')->get();

        $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query();

        $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if($request->company_id){

            $assetqry->where('company_id', $request->company_id);

            $branches=Branch::visibleToUser(auth()->user())->where('status','active')->where('company_id', $request->company_id)->select('id','name')->get();

        }

        if ($request->branch_id) {
            $assetqry->filteredByBranch((int) $request->branch_id);

            $locations = BranchLocation::where('status', 'active')->where('branch_id', $request->branch_id)->select('id', 'location_name')->orderBy('location_name')->get();
        }

        if($request->vendor_id){

            $assetqry->where('supplier', $request->vendor_id);

        }

        if($request->location_id){

            $assetqry->where('location_id', $request->location_id);

        }

        if($request->status){

            $assetqry->where('status', $request->status);

        }

        if($request->condition){    

            $assetqry->where('condition', $request->condition);

        }

        if($request->operational_status){    

            $assetqry->where('operational_status', $request->operational_status);    

        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','company_id','branch_id','location_id','vendor_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Branchwise Assets')) {
            return $export;
        }

        return view('reports.branchwiseasset',compact('companies','branches','locations','vendors','assets','statuses','conditions','operations_status','tableColumns'));

    }


    public function brandwiseassets(Request $request){

        $companies = Company::visibleToUser(auth()->user())->active()->select('id', 'name')->get();

        $branches = collect();

        $brands = Brand::active()->orderBy('brand_name')->get(['id', 'brand_name']);

        $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query(); 

        $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if ($request->company_id) {
            $assetqry->where('company_id', $request->company_id);
            $branches = Branch::visibleToUser(auth()->user())->where('status', 'active')->where('company_id', $request->company_id)->select('id', 'name')->orderBy('name')->get();
        }

        if ($request->branch_id) {
            $assetqry->filteredByBranch((int) $request->branch_id);
        }

        $needle = trim((string) $request->input('brand_search', ''));
        if ($needle !== '') {
            $assetqry->whereHas('brand', static function ($q) use ($needle) {
                $q->where('brand_name', 'like', '%'.addcslashes($needle, '%_\\').'%');
            });
        }

        if($request->status){

            $assetqry->where('status', $request->status);

        }

        if($request->condition){    

            $assetqry->where('condition', $request->condition);

        }

        if($request->operational_status){    

            $assetqry->where('operational_status', $request->operational_status);    

        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','company_id','branch_id','brand_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Brandwise Assets')) {
            return $export;
        }

        return view('reports.brandwiseassets', compact('assets', 'brands', 'companies', 'branches', 'statuses', 'conditions', 'operations_status', 'tableColumns'));


    }

    public function vendorwiseassets(Request $request){

        $companies=Company::visibleToUser(auth()->user())->active()->select('id','name')->get();

        $branches=collect();

        $vendors=Vendor::active()->get();

        $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query(); 

        $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if ($request->company_id) {

            $assetqry->where('company_id', $request->company_id);

            $branches=Branch::visibleToUser(auth()->user())->where('status','active')->where('company_id', $request->company_id)->select('id','name')->orderBy('name')->get();

        }

        if ($request->branch_id) {
            $assetqry->filteredByBranch((int) $request->branch_id);
        }

        if($request->vendor_id){

            $assetqry->where('supplier', $request->vendor_id);

        } 

        if($request->status){

            $assetqry->where('status', $request->status);

        }    

        if($request->condition){    

            $assetqry->where('condition', $request->condition);

        }    

        if($request->operational_status){    

            $assetqry->where('operational_status', $request->operational_status);    

        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','company_id','branch_id','vendor_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Vendorwise Assets')) {
            return $export;
        }

        return view('reports.vendorwiseassets',compact('companies','branches','assets','vendors','statuses','conditions','operations_status','tableColumns'));


    }


    public function locationwiseassets(Request $request){

        $companies=Company::visibleToUser(auth()->user())->active()->select('id','name')->get();

        $branches=collect();

        $locations=collect();

        $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query();

        $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if($request->company_id){

            $assetqry->where('company_id', $request->company_id);

            $branches=Branch::visibleToUser(auth()->user())->where('status','active')->where('company_id', $request->company_id)->select('id','name')->get();

        }

        if ($request->branch_id) {
            $assetqry->filteredByBranch((int) $request->branch_id);

            $locations = BranchLocation::where('status', 'active')->where('branch_id', $request->branch_id)->select('id', 'location_name')->get();
        }

        if($request->location_id){

            $assetqry->where('location_id', $request->location_id);

        }

        if($request->status){

            $assetqry->where('status', $request->status);

        }   

        if($request->condition){    

            $assetqry->where('condition', $request->condition);

        }

        if($request->operational_status){

            $assetqry->where('operational_status', $request->operational_status);

        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','company_id','branch_id','location_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Locationwise Assets')) {
            return $export;
        }

        return view('reports.locationwiseassets',compact('assets','companies','branches','locations','statuses','conditions','operations_status','tableColumns'));


    }

    public function categorywiseassets(Request $request){

        $categories=AssetCategory::active()->WhereNULL('parent_id')->get();

        $subcategories=collect();

        $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query();

         $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if($request->category_id){

            $assetqry->where('category_id', $request->category_id);

            $subcategories=AssetCategory::where('status','active')->where('parent_id', $request->category_id)->select('id','category_name')->get();

        }

        if($request->sub_category_id){

            $assetqry->where('sub_category_id', $request->sub_category_id);

        }

        if($request->status){

            $assetqry->where('status', $request->status);

        }

        if($request->condition){    

            $assetqry->where('condition', $request->condition);

        }

        if($request->operational_status){

            $assetqry->where('operational_status', $request->operational_status);

        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Categorywise Assets')) {
            return $export;
        }

        return view('reports.categorywiseassets',compact('subcategories','assets','categories','statuses','conditions','operations_status','tableColumns'));


    }


    public function typewiseassets(Request $request){

        $categories=AssetCategory::active()->WhereNULL('parent_id')->get();

        $subcategories=collect();

        $assetTypes=collect();

        $statuses=AssetStatus::cases();

        $conditions=AssetCondition::cases();

        $operations_status=AssetOperationalStatus::cases();

        $assetqry=Asset::query();

         $assetqry->VisibleToUser(auth()->user());

        $this->eagerLoadAssetReportRelations($assetqry);

        if($request->category_id){

            $assetqry->where('category_id', $request->category_id);

            $subcategories=AssetCategory::where('status','active')->where('parent_id', $request->category_id)->select('id','category_name')->get();

        }

        if($request->sub_category_id){

            $assetqry->where('sub_category_id', $request->sub_category_id);

            $assetTypes=AssetType::where('status','active')->where('sub_category_id', $request->sub_category_id)->select('id','asset_type_name')->get();

        }

        if($request->asset_type_id){

            $assetqry->where('asset_type_id', $request->asset_type_id);
        }

        if($request->status){

            $assetqry->where('status', $request->status);

        }

        if($request->condition){

            $assetqry->where('condition', $request->condition);
        }

        if($request->operational_status){

            $assetqry->where('operational_status', $request->operational_status);
        }

        if($request->from_date){
            $assetqry->whereDate('purchase_date', '>=', $request->from_date);
        }

        if($request->to_date){
            $assetqry->whereDate('purchase_date', '<=', $request->to_date);
        }

        $tableColumns=array('serial_no','asset_code','status','sub_category_id','asset_type_id','condition','operational_status');

        $assets = $this->assetReportResults($assetqry, $request);

        if ($export = $this->maybeExportAssetReport($request, $assets, $tableColumns, 'Typewise Assets')) {
            return $export;
        }

        return view('reports.typewiseassets',compact('subcategories','assetTypes','assets','categories','statuses','conditions','operations_status','tableColumns'));

        
    }


    /** Eager relations used by asset report grids (avoid N+1 on category / lookups). */
    protected function eagerLoadAssetReportRelations(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->with([
            'subCategory',
            'assetType',
            'company',
            'branch',
            'brand',
            'vendor',
            'location',
        ]);
    }

    /** Paginated list (50/page) for screen; full collection when exporting. */
    protected function assetReportResults(Builder $assetqry, Request $request)
    {
        $ordered = $assetqry->orderBy('id', 'DESC');

        if (in_array($request->input('export_format'), ['csv', 'pdf'], true)) {
            return $ordered->get();
        }

        return $ordered->paginate(self::REPORT_PER_PAGE)->withQueryString();
    }

    protected function maybeExportAssetReport(
        Request $request,
        \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator $assets,
        array $tableColumns,
        string $reportTitle
    ): StreamedResponse|Response|null {
        $format = $request->input('export_format');

        if (! in_array($format, ['csv', 'pdf'], true)) {
            return null;
        }

        $rows = $assets instanceof \Illuminate\Support\Collection ? $assets : collect($assets->items());
        $exportService = app(AssetReportExportService::class);
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $reportTitle) ?: 'asset-report');

        if ($format === 'csv') {
            return $exportService->downloadCsv($rows, $tableColumns, $slug.'-'.now()->format('Y-m-d').'.csv');
        }

        return response()->view('reports.exports.assets-print', [
            'assets' => $rows,
            'tableColumns' => $tableColumns,
            'reportTitle' => $reportTitle,
        ]);
    }


}
