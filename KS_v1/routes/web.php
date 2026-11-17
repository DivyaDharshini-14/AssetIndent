<?php
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Dashboard\Analytics;
use App\Http\Controllers\DealerLocation\DealerLocationController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Designation\DesignationController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\FuelType\FuelTypeController;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\Lead\EnquiryTypeController;
use App\Http\Controllers\Lead\LeadSourceController;
use App\Http\Controllers\Lead\LeadStatusController;
use App\Http\Controllers\Lead\LeadTypeController;
use App\Http\Controllers\Lead\StatusSourceController;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Segment\SegmentController;
use App\Http\Controllers\Segment\SubSegmentController;
use App\Http\Controllers\Transmission\TransmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\VehicleModel\VehicleModelController;
use App\Http\Controllers\Brands\BrandController;
use App\Http\Controllers\AssetTypes\AssetTypeController;
use App\Http\Controllers\Location\BranchLocationController;
use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Assets\AssetAssignmentController;
use App\Http\Controllers\Assets\AssetWarrantyController;
use App\Http\Controllers\Employee\EmployeesController;
use App\Http\Controllers\Employee\EmployeeStaffController;
use App\Http\Controllers\Assets\AssetCategoryController;
use App\Http\Controllers\Assets\AssetNameController;
use App\Http\Controllers\Assets\AssetDocumentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Assets\AssetMaintenanceController;
use App\Http\Controllers\Assets\AssetReplacementController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Dashboard\AssetDashboardController;
use App\Http\Controllers\ApprovalTemplateController;
use App\Http\Controllers\IndentController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcurementDashboardController;
use App\Http\Controllers\ProcurementReportController;
use App\Http\Controllers\PurchaseOfficerDashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\VendorQuotationController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\QualityInspectionController;
use Illuminate\Support\Facades\Route;

// layout
Route::get('seed-item-perms', function() { require base_path('seed_item_permissions.php'); return 'done'; });

Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// pages
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

// authentication
Route::get('/login', [LoginBasic::class, 'index'])->name('login');
Route::post('/login', [LoginBasic::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [Analytics::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard:view');

    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');





    // ========== COMPANY MASTER ==========

    Route::prefix('companies')->name('companies.')->group(function () {

        Route::get('/', [CompanyController::class, 'index'])->name('index')->middleware('permission:companies:view');
        Route::get('/create', [CompanyController::class, 'create'])->name('create')->middleware('permission:companies:create');
        Route::post('/', [CompanyController::class, 'store'])->name('store')->middleware('permission:companies:create');
        Route::get('/{company}', [CompanyController::class, 'show'])->name('show')->middleware('permission:companies:view');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit')->middleware('permission:companies:edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('update')->middleware('permission:companies:edit');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy')->middleware('permission:companies:delete');
    });

    // =============== Roles And Permisson Routes ================
    Route::prefix('roles')->name('roles.')->group(function () {

        Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:roles:view');
        Route::get('create', [RoleController::class, 'create'])->name('create')->middleware('permission:roles:create');
        Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:roles:create');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:roles:edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:roles:edit');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:roles:delete');
    });

    Route::prefix('permissions')->name('permissions.')->group(function () {

        Route::get('/', [PermissionController::class, 'index'])->name('index')->middleware('permission:permissions:view');

        Route::middleware(['permission:permissions:create'])->group(function () {
            Route::get('create', [PermissionController::class, 'create'])->name('create');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
        });

        Route::middleware(['permission:permissions:edit'])->group(function () {
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        });

        Route::middleware(['permission:permissions:delete'])->group(function () {
            Route::post('/delete-duplicates', [PermissionController::class, 'deleteDuplicates'])->name('delete-duplicates');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('users')->name('users.')->group(function () {

        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:users:view');
        Route::get('create', [UserController::class, 'create'])->name('create')->middleware('permission:users:create');
        Route::get('{user}', [UserController::class, 'show'])->name('show')->middleware('permission:users:view');
        Route::post('', [UserController::class, 'store'])->name('store')->middleware('permission:users:create');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('permission:users:edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update')->middleware('permission:users:edit');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:users:delete');
    });

    Route::get('users/{user}/roles', [UserRoleController::class, 'edit'])->name('users.roles.edit')->middleware('permission:users:edit');
    Route::post('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update')->middleware('permission:users:edit');

    // ========== DEALER LOCATION ==========
    Route::prefix('dealer-locations')->name('dealer-locations.')->group(function () {

        Route::get('/', [DealerLocationController::class, 'index'])->name('index')->middleware('permission:dealer-locations:view');
        Route::get('create', [DealerLocationController::class, 'create'])->name('create')->middleware('permission:dealer-locations:create');
        Route::post('/', [DealerLocationController::class, 'store'])->name('store')->middleware('permission:dealer-locations:create');
        Route::get('/{dealerLocation}', [DealerLocationController::class, 'show'])->name('show')->middleware('permission:dealer-locations:view');
        Route::get('/{dealerLocation}/edit', [DealerLocationController::class, 'edit'])->name('edit')->middleware('permission:dealer-locations:edit');
        Route::put('/{dealerLocation}', [DealerLocationController::class, 'update'])->name('update')->middleware('permission:dealer-locations:edit');
        Route::delete('/{dealerLocation}', [DealerLocationController::class, 'destroy'])->name('destroy')->middleware('permission:dealer-locations:delete');
    });

    // ========== SEGMENTS ==========

    Route::prefix('segments')->name('segments.')->group(function () {

        Route::get('/', [SegmentController::class, 'index'])->name('index')->middleware('permission:segments:view');
        Route::get('/create', [SegmentController::class, 'create'])->name('create')->middleware('permission:segments:create');
        Route::post('/', [SegmentController::class, 'store'])->name('store')->middleware('permission:segments:create');
        Route::get('/{segment}', [SegmentController::class, 'show'])->name('show')->middleware('permission:segments:view');
        Route::get('/{segment}/edit', [SegmentController::class, 'edit'])->name('edit')->middleware('permission:segments:edit');
        Route::put('/{segment}', [SegmentController::class, 'update'])->name('update')->middleware('permission:segments:edit');
        Route::delete('/{segment}', [SegmentController::class, 'destroy'])->name('destroy')->middleware('permission:segments:delete');
        Route::get('/check-code/{code}', [SegmentController::class, 'checkCode'])->name('check-code')->middleware('permission:segments:checkcode');
    });

    // ========== TRANSMISSIONS ==========
    Route::prefix('transmissions')->name('transmissions.')->group(function () {

        Route::get('/', [TransmissionController::class, 'index'])
            ->name('index')
            ->middleware('permission:transmissions:view');

        Route::get('/create', [TransmissionController::class, 'create'])
            ->name('create')
            ->middleware('permission:transmissions:create');

        Route::post('/', [TransmissionController::class, 'store'])
            ->name('store')
            ->middleware('permission:transmissions:create');

        Route::get('/{transmission}', [TransmissionController::class, 'show'])
            ->name('show')
            ->middleware('permission:transmissions:view');

        Route::get('/{transmission}/edit', [TransmissionController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:transmissions:edit');

        Route::put('/{transmission}', [TransmissionController::class, 'update'])
            ->name('update')
            ->middleware('permission:transmissions:edit');

        Route::delete('/{transmission}', [TransmissionController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:transmissions:delete');
    });

    // ========== FUEL TYPES ==========
    Route::prefix('fuel-types')->name('fuel-types.')->group(function () {

        Route::get('/', [FuelTypeController::class, 'index'])
            ->name('index')
            ->middleware('permission:fuel-types:view');

        Route::get('/create', [FuelTypeController::class, 'create'])
            ->name('create')
            ->middleware('permission:fuel-types:create');

        Route::post('/', [FuelTypeController::class, 'store'])
            ->name('store')
            ->middleware('permission:fuel-types:create');

        Route::get('/{fuelType}', [FuelTypeController::class, 'show'])
            ->name('show')
            ->middleware('permission:fuel-types:view');

        Route::get('/{fuelType}/edit', [FuelTypeController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:fuel-types:edit');

        Route::put('/{fuelType}', [FuelTypeController::class, 'update'])
            ->name('update')
            ->middleware('permission:fuel-types:edit');

        Route::delete('/{fuelType}', [FuelTypeController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:fuel-types:delete');
    });

    // ========== VEHICLE MODELS ==========
    Route::prefix('vehicle-models')->name('vehicle-models.')->group(function () {

        Route::get('/', [VehicleModelController::class, 'index'])
            ->name('index')
            ->middleware('permission:vehicle-models:view');

        Route::get('/create', [VehicleModelController::class, 'create'])
            ->name('create')
            ->middleware('permission:vehicle-models:create');

        Route::post('/', [VehicleModelController::class, 'store'])
            ->name('store')
            ->middleware('permission:vehicle-models:create');

        Route::get('/{vehicleModel}', [VehicleModelController::class, 'show'])
            ->name('show')
            ->middleware('permission:vehicle-models:view');

        Route::get('/{vehicleModel}/edit', [VehicleModelController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:vehicle-models:edit');

        Route::put('/{vehicleModel}', [VehicleModelController::class, 'update'])
            ->name('update')
            ->middleware('permission:vehicle-models:edit');

        Route::delete('/{vehicleModel}', [VehicleModelController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:vehicle-models:delete');
    });

    // ========== ENQUIRY TYPES ==========
    Route::prefix('enquiry-types')->name('enquiry-types.')->group(function () {

        Route::get('/', [EnquiryTypeController::class, 'index'])
            ->name('index')
            ->middleware('permission:enquiry-types:view');

        Route::get('/create', [EnquiryTypeController::class, 'create'])
            ->name('create')
            ->middleware('permission:enquiry-types:create');

        Route::post('/', [EnquiryTypeController::class, 'store'])
            ->name('store')
            ->middleware('permission:enquiry-types:create');

        Route::get('/{enquiryType}/edit', [EnquiryTypeController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:enquiry-types:edit');

        Route::put('/{enquiryType}', [EnquiryTypeController::class, 'update'])
            ->name('update')
            ->middleware('permission:enquiry-types:edit');

        Route::delete('/{enquiryType}', [EnquiryTypeController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:enquiry-types:delete');
    });

    // ========== LEAD TYPES ==========
    Route::prefix('lead-types')->name('lead-types.')->group(function () {

        Route::get('/', [LeadTypeController::class, 'index'])
            ->name('index')
            ->middleware('permission:lead-types:view');

        Route::get('/create', [LeadTypeController::class, 'create'])
            ->name('create')
            ->middleware('permission:lead-types:create');

        Route::post('/', [LeadTypeController::class, 'store'])
            ->name('store')
            ->middleware('permission:lead-types:create');

        Route::get('/{leadType}/edit', [LeadTypeController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:lead-types:edit');

        Route::put('/{leadType}', [LeadTypeController::class, 'update'])
            ->name('update')
            ->middleware('permission:lead-types:edit');

        Route::delete('/{leadType}', [LeadTypeController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:lead-types:delete');
    });

    // ========== LEAD STATUSES ==========
    Route::prefix('lead-statuses')->name('lead-statuses.')->group(function () {

        Route::get('/', [LeadStatusController::class, 'index'])
            ->name('index')
            ->middleware('permission:lead-statuses:view');

        Route::get('/create', [LeadStatusController::class, 'create'])
            ->name('create')
            ->middleware('permission:lead-statuses:create');

        Route::post('/', [LeadStatusController::class, 'store'])
            ->name('store')
            ->middleware('permission:lead-statuses:create');

        Route::get('/{leadStatus}/edit', [LeadStatusController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:lead-statuses:edit');

        Route::put('/{leadStatus}', [LeadStatusController::class, 'update'])
            ->name('update')
            ->middleware('permission:lead-statuses:edit');

        Route::delete('/{leadStatus}', [LeadStatusController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:lead-statuses:delete');
    });

    // ========== LEAD SOURCES ==========
    Route::prefix('lead-sources')->name('lead-sources.')->group(function () {

        Route::get('/', [LeadSourceController::class, 'index'])
            ->name('index')
            ->middleware('permission:lead-sources:view');

        Route::get('/create', [LeadSourceController::class, 'create'])
            ->name('create')
            ->middleware('permission:lead-sources:create');

        Route::post('/', [LeadSourceController::class, 'store'])
            ->name('store')
            ->middleware('permission:lead-sources:create');

        Route::get('/{leadSource}/edit', [LeadSourceController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:lead-sources:edit');

        Route::put('/{leadSource}', [LeadSourceController::class, 'update'])
            ->name('update')
            ->middleware('permission:lead-sources:edit');

        Route::delete('/{leadSource}', [LeadSourceController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:lead-sources:delete');
    });

    // ========== STATUS SOURCES ==========
    Route::prefix('status-sources')->name('status-sources.')->group(function () {

        Route::get('/', [StatusSourceController::class, 'index'])
            ->name('index')
            ->middleware('permission:status-sources:view');

        Route::get('/create', [StatusSourceController::class, 'create'])
            ->name('create')
            ->middleware('permission:status-sources:create');

        Route::post('/', [StatusSourceController::class, 'store'])
            ->name('store')
            ->middleware('permission:status-sources:create');

        Route::get('/{statusSource}/edit', [StatusSourceController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:status-sources:edit');

        Route::put('/{statusSource}', [StatusSourceController::class, 'update'])
            ->name('update')
            ->middleware('permission:status-sources:edit');

        Route::delete('/{statusSource}', [StatusSourceController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:status-sources:delete');
    });

    // ========== DESIGNATION ==========
    Route::prefix('designation')->name('designation.')->group(function () {

        Route::get('/', [DesignationController::class, 'index'])
            ->name('index')
            ->middleware('permission:designation:view');

        Route::get('/create', [DesignationController::class, 'create'])
            ->name('create')
            ->middleware('permission:designation:create');

        Route::get('/{designation}/edit', [DesignationController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:designation:edit');

        Route::post('/', [DesignationController::class, 'store'])
            ->name('store')
            ->middleware('permission:designation:create');

        Route::delete('/{designation}', [DesignationController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:designation:delete');

        Route::put('/{designation}', [DesignationController::class, 'update'])
            ->name('update')
            ->middleware('permission:designation:edit');

        Route::put('/', [DesignationController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:designation:status');
    });

    // ========== DEPARTMENT ==========
    Route::prefix('department')->name('department.')->group(function () {

        Route::get('/', [DepartmentController::class, 'index'])
            ->name('index')
            ->middleware('permission:department:view');

        Route::get('/create', [DepartmentController::class, 'create'])
            ->name('create')
            ->middleware('permission:department:create');

        Route::post('/', [DepartmentController::class, 'store'])
            ->name('store')
            ->middleware('permission:department:create');

        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:department:edit');

        Route::put('/{department}', [DepartmentController::class, 'update'])
            ->name('update')
            ->middleware('permission:department:edit');

        Route::delete('/{department}', [DepartmentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:department:delete');

        Route::put('/', [DepartmentController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:department:status');;
    });

    Route::prefix('subsegment')->name('subsegment.')->group(function () {

        Route::get('/', [SubSegmentController::class, 'index'])
            ->name('index')
            ->middleware('permission:subsegments:view');

        Route::get('/create', [SubSegmentController::class, 'create'])
            ->name('create')
            ->middleware('permission:subsegments:create');

        Route::post('/', [SubSegmentController::class, 'store'])
            ->name('store')
            ->middleware('permission:subsegments:create');

        Route::get('/{subsegment}', [SubSegmentController::class, 'show'])
            ->name('show')
            ->middleware('permission:subsegments:view');

        Route::get('/{subsegment}/edit', [SubSegmentController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:subsegments:edit');

        Route::put('/{subsegment}', [SubSegmentController::class, 'update'])
            ->name('update')
            ->middleware('permission:subsegments:edit');

        Route::delete('/{subsegment}', [SubSegmentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:subsegments:delete');

        Route::put('/', [SubSegmentController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:subsegments:status');;
    });

    /*

    Route::prefix('employee')->name('employee.')->group(function () {

        Route::get('/', [EmployeeController::class, 'index'])
            ->name('index')
            ->middleware('permission:employee:view');

        Route::get('/create', [EmployeeController::class, 'create'])
            ->name('create')
            ->middleware('permission:employee:create');

        Route::post('/', [EmployeeController::class, 'store'])
            ->name('store')
            ->middleware('permission:employee:create');

        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:employee:edit');

        Route::post('/{employee}/update', [EmployeeController::class, 'update'])
            ->name('update')
            ->middleware('permission:employee:edit');

        Route::get('/{employee}/bySegment', [EmployeeController::class, 'getBySegment'])
            ->name('bySegment')
            ->middleware('permission:employee:view');

        Route::get('/{employee}/byLocations', [EmployeeController::class, 'byLocations'])
            ->name('byLocations')
            ->middleware('permission:employee:view');

        Route::get('by-multiple-companies', [EmployeeController::class, 'byMultipleCompanies'])
            ->name('byMultipleCompanies')->middleware('permission:employee:view');

    }); */


    Route::get('dashboard/asset', [AssetDashboardController::class, 'index'])->name('dashboard.asset');

    Route::resource('employees', EmployeesController::class)->names('employees');

    Route::resource('employee-staff', EmployeeStaffController::class)
        ->names('employee-staff')
        ->parameters(['employee-staff' => 'employee']);

    Route::get('ajax/getbranch/withoutscope/{company}', [AjaxController::class, 'getBranchesWithoutScope'])->name('ajax.getbranch.withoutscope');

    Route::post('ajax/getbranch/withoutscope/bycompanies', [AjaxController::class, 'getBrancheswithoutScopeByCompanies'])->name('ajax.getbranch.withoutscope.bycompanies');

    Route::post('ajax/getbranch/bycompanies', [AjaxController::class, 'getBranchesByCompanies'])->name('ajax.getbranch.bycompanies');

    Route::get('ajax/getbranch/bycompany/{company}', [AjaxController::class, 'getBranches'])->name('ajax.getbranch.bycompany');

    Route::get('ajax/getlocation/bybranch/{branch}', [AjaxController::class, 'getLocations'])->name('ajax.getlocation.bybranch');

    Route::get('ajax/getasset/bybranch/{branch}', [AjaxController::class, 'getAssets'])->name('ajax.getasset.bybranch');

    Route::get('ajax/getusers/bybranch/{branch}', [AjaxController::class, 'getUsers'])->name('ajax.getusers.bybranch');
    Route::get('ajax/getdepartments/bybranch/{branch}', [AjaxController::class, 'getDepartmentsByBranch'])->name('ajax.getdepartments.bybranch');

    Route::get('ajax/getsubcategory/bycategory/{category}', [AjaxController::class, 'getSubCategories'])->name('ajax.getsubcategory.bycategory');

    Route::get('ajax/getassettype/byassetcategory/{category}', [AjaxController::class, 'getAssetTypes'])->name('ajax.getassettype.byassetcategory');

    Route::put('brands', [BrandController::class, 'updateStatus'])
        ->name('brands.status');

    Route::post('brand/quick-store', [BrandController::class, 'quickStore'])->name('brand.quick-store');

    Route::resource('brands', BrandController::class);

    Route::put('asset-types/status', [AssetTypeController::class, 'updateStatus'])->name('asset-types.status');

    Route::resource('asset-types', AssetTypeController::class);

    Route::get('asset-names/suggest', [AssetNameController::class, 'suggest'])->name('asset-names.suggest');
    Route::post('asset-names/quick-store', [AssetNameController::class, 'quickStore'])->name('asset-names.quick-store');
    Route::put('asset-names/status', [AssetNameController::class, 'updateStatus'])->name('asset-names.status');
    Route::resource('asset-names', AssetNameController::class);

    Route::put('location', [BranchLocationController::class, 'updateStatus'])->name('location.status');

    Route::resource('location', BranchLocationController::class);

    Route::post('branch/quick-store', [BranchController::class, 'quickStore'])->name('branch.quick-store');

    Route::resource('branch', BranchController::class);

    Route::put('asset-category/status', [AssetCategoryController::class, 'updateStatus'])->name('asset-category.status');

    Route::resource('asset-category', AssetCategoryController::class);

    Route::get('item-masters/get-asset-types', [\App\Http\Controllers\ItemMasterController::class, 'getAssetTypes'])->name('item-masters.get-asset-types');
    Route::resource('item-masters', \App\Http\Controllers\ItemMasterController::class);

    Route::post('asset/assignment/bulk', [AssetAssignmentController::class, 'assignBulkProcess'])->name('asset.assign.bulk.process');

    Route::get('asset/assignment/bulk', [AssetAssignmentController::class, 'assignBulk'])->name('asset.assign.bulk');

    Route::get('asset/assignment/assign-targets/{type}', [AssetAssignmentController::class, 'getTargets'])->name('asset.assign.targets');

    Route::get('asset/assignment/accept/{assignment}', [AssetAssignmentController::class, 'acceptassignment'])->name('asset.assignment.accept');

    Route::put('asset/assignment/accept/{assignment}', [AssetAssignmentController::class, 'acceptassetassignment'])->name('asset.assignment.accept.update');

    Route::get('asset/assignment/reject/{assignment}', [AssetAssignmentController::class, 'rejectassignment'])->name('asset.assignment.reject');

    Route::put('asset/assignment/reject/{assignment}', [AssetAssignmentController::class, 'rejectassetassignment'])->name('asset.assignment.reject.update');

    Route::get('asset/assignment/returnaccept/{assignment}', [AssetAssignmentController::class, 'acceptReturn'])->name('asset.assignment.return.accept');

    Route::put('asset/assignment/returnaccept/{assignment}', [AssetAssignmentController::class, 'acceptReturnUpdate'])->name('asset.assignment.return.accept.update');

    Route::get('asset/assignment/return/{assignment}', [AssetAssignmentController::class, 'returnassignment'])->name('asset.assignment.return');

    Route::put('asset/assignment/return/{assignment}', [AssetAssignmentController::class, 'returnassetassignment'])->name('asset.assignment.return.confirm');

    Route::resource('asset/assignment', AssetAssignmentController::class)->names('asset.assignment');

    Route::resource('asset/warranties', AssetWarrantyController::class)->names('asset.warranty');

    Route::resource('asset/document', AssetDocumentController::class)->names('asset.document');

    Route::resource('asset/maintenance', AssetMaintenanceController::class)->names('asset.maintenance');

    Route::resource('asset/asset-replacement', AssetReplacementController::class)->names('asset.replacement');

    Route::get('asset-depreciation', [AssetController::class, 'depreciation'])->name('asset.depreciation');

    Route::get('asset/import/move-valid-data', [AssetController::class, 'moveValidData'])->name('asset.import.movevaliddata');

    Route::get('asset/import/preview', [AssetController::class, 'importPreview'])->name('asset.import.preview');

    Route::get('asset/import', [AssetController::class, 'import'])->name('asset.import');

    Route::post('asset/import', [AssetController::class, 'importAsset'])->name('asset.import.process');

    Route::resource('asset', AssetController::class);

    Route::post('vendor/quick-store', [VendorController::class, 'quickStore'])->name('vendor.quick-store');

    Route::resource('brands', BrandController::class);

    Route::resource('vendor', VendorController::class);

    Route::resource('settings', SettingsController::class);

    // Indent Management Routes
    Route::prefix('indents')->name('indents.')->group(function () {
        Route::get('/dashboard', [IndentController::class, 'dashboard'])
            ->name('dashboard')
            ->middleware('permission:indents:view');
        Route::get('/approvals', [IndentController::class, 'approvalsDashboard'])
            ->name('approvals')
            ->middleware('permission:indents:view');
        Route::get('/', [IndentController::class, 'index'])
            ->name('index')
            ->middleware('permission:indents:view');
        Route::get('/create', [IndentController::class, 'create'])
            ->name('create')
            ->middleware('permission:indents:create');
        Route::get('/items/suggest', [IndentController::class, 'suggestItems'])
            ->name('items.suggest')
            ->middleware('permission:indents:create|indents:edit');
        Route::get('/assets/search', [IndentController::class, 'searchMyAssets'])
            ->name('assets.search')
            ->middleware('permission:indents:create|indents:edit');
        Route::get('/fields/suggest', [IndentController::class, 'suggestFields'])
            ->name('fields.suggest')
            ->middleware('permission:indents:create|indents:edit');
        Route::post('/', [IndentController::class, 'store'])
            ->name('store')
            ->middleware('permission:indents:create');
        Route::get('/{indent}', [IndentController::class, 'show'])
            ->name('show')
            ->middleware('permission:indents:view');
        Route::get('/{indent}/edit', [IndentController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:indents:edit|indents:create');
        Route::put('/{indent}', [IndentController::class, 'update'])
            ->name('update')
            ->middleware('permission:indents:edit|indents:create');
        Route::delete('/{indent}', [IndentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:indents:delete');
        // Authorization for approve/reject/send-back is enforced inside ApprovalWorkflowService
        // against the indent's currently assigned_permission (dynamic per workflow step).
        Route::post('/{indent}/approve', [IndentController::class, 'approve'])
            ->name('approve')
            ->middleware('permission:indents:approve|indents:view');
        Route::post('/{indent}/reject', [IndentController::class, 'reject'])
            ->name('reject')
            ->middleware('permission:indents:reject|indents:approve|indents:view');
        Route::post('/{indent}/send-back', [IndentController::class, 'sendBack'])
            ->name('send-back')
            ->middleware('permission:indents:approve|indents:view');
        Route::post('/{indent}/submit', [IndentController::class, 'submit'])->name('submit')->middleware('permission:indents:create');
        Route::post('/{indent}/cancel', [IndentController::class, 'cancel'])->name('cancel')->middleware('permission:indents:edit');
        Route::get('/{indent}/print', [IndentController::class, 'print'])->name('print')->middleware('permission:indents:view');
        Route::get('/{indent}/pdf', [IndentController::class, 'pdf'])->name('pdf')->middleware('permission:indents:view');
        Route::get('/{indent}/excel', [IndentController::class, 'excel'])->name('excel')->middleware('permission:indents:view');
    });

    // Settings → Approval Templates (permission-based; assign via Roles)
    Route::prefix('approval-templates')->name('approval-templates.')->middleware('permission:approval-templates:view|approval-templates:manage')->group(function () {
        Route::get('/', [ApprovalTemplateController::class, 'index'])->name('index');
        Route::post('/', [ApprovalTemplateController::class, 'store'])
            ->name('store')
            ->middleware('permission:approval-templates:create|approval-templates:manage');
        Route::put('/{approvalTemplate}', [ApprovalTemplateController::class, 'update'])
            ->name('update')
            ->middleware('permission:approval-templates:edit|approval-templates:manage');
        Route::post('/{approvalTemplate}/toggle', [ApprovalTemplateController::class, 'toggle'])
            ->name('toggle')
            ->middleware('permission:approval-templates:edit|approval-templates:manage');
        Route::delete('/{approvalTemplate}', [ApprovalTemplateController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:approval-templates:delete|approval-templates:manage');
        Route::post('/{approvalTemplate}/steps', [ApprovalTemplateController::class, 'storeStep'])
            ->name('steps.store')
            ->middleware('permission:approval-templates:edit|approval-templates:manage');
        Route::put('/steps/{step}', [ApprovalTemplateController::class, 'updateStep'])
            ->name('steps.update')
            ->middleware('permission:approval-templates:edit|approval-templates:manage');
        Route::post('/steps/{step}/move', [ApprovalTemplateController::class, 'moveStep'])
            ->name('steps.move')
            ->middleware('permission:approval-templates:edit|approval-templates:manage');
        Route::delete('/steps/{step}', [ApprovalTemplateController::class, 'destroyStep'])
            ->name('steps.destroy')
            ->middleware('permission:approval-templates:edit|approval-templates:manage');
    });

    Route::get('procurement/dashboard', [ProcurementDashboardController::class, 'index'])
        ->name('procurement.dashboard')
        ->middleware('permission:procurement.dashboard:view|purchase-management.dashboard|indents:view|purchase-requisition.view|purchase-requisitions:view|grn.view|grns:view');

    // Purchase Management (Purchase Officer)
    Route::get('purchase-management/dashboard', [PurchaseOfficerDashboardController::class, 'index'])
        ->name('purchase-management.dashboard')
        ->middleware('permission:purchase-management.dashboard|purchase-requisition.view|purchase-requisitions:view');

    Route::prefix('purchase-requisitions')->name('purchase-requisitions.')->middleware('permission:purchase-requisition.view|purchase-requisitions:view')->group(function () {
        Route::get('/', [PurchaseRequisitionController::class, 'index'])->name('index');
        Route::get('/{purchaseRequisition}/print', [PurchaseRequisitionController::class, 'print'])->name('print');
        Route::post('/{purchaseRequisition}/close', [PurchaseRequisitionController::class, 'close'])
            ->name('close')
            ->middleware('permission:purchase-requisition.process|purchase-requisitions:process|purchase-requisition.close|purchase-requisition.manual-close|purchase-requisitions:manual-close');
        Route::post('/{purchaseRequisition}/transfer', [PurchaseRequisitionController::class, 'transfer'])
            ->name('transfer')
            ->middleware('permission:purchase-requisition.process|purchase-requisitions:process|purchase-requisition.transfer-from-store|purchase-requisitions:transfer-from-store');
        Route::get('/{purchaseRequisition}/transfer-availability', [PurchaseRequisitionController::class, 'transferAvailability'])
            ->name('transfer-availability')
            ->middleware('permission:purchase-requisition.process|purchase-requisitions:process|purchase-requisition.transfer-from-store|purchase-requisitions:transfer-from-store');
        Route::post('/{purchaseRequisition}/process', [PurchaseRequisitionController::class, 'process'])
            ->name('process')
            ->middleware('permission:purchase-requisition.process|purchase-requisitions:process');
        Route::get('/{purchaseRequisition}', [PurchaseRequisitionController::class, 'show'])->name('show');
    });

    Route::prefix('rfqs')->name('rfqs.')->middleware('permission:rfq.view|rfqs:view|rfq.create|rfqs:create|purchase-requisition.proceed-to-rfq|purchase-requisitions:proceed-to-rfq|purchase-requisition.process|purchase-requisitions:process')->group(function () {
        Route::get('/', [RfqController::class, 'index'])->name('index');
        Route::get('/create', [RfqController::class, 'create'])->name('create')->middleware('permission:rfq.create|rfqs:create|purchase-requisition.proceed-to-rfq|purchase-requisitions:proceed-to-rfq|purchase-requisition.process|purchase-requisitions:process');
        Route::post('/', [RfqController::class, 'store'])->name('store')->middleware('permission:rfq.create|rfqs:create|purchase-requisition.proceed-to-rfq|purchase-requisitions:proceed-to-rfq|purchase-requisition.process|purchase-requisitions:process');
        Route::get('/purchase-requisitions/{purchaseRequisition}/data', [RfqController::class, 'prData'])->name('purchase-requisitions.data')->middleware('permission:rfq.create|rfqs:create|purchase-requisition.proceed-to-rfq|purchase-requisitions:proceed-to-rfq|purchase-requisition.process|purchase-requisitions:process');
        Route::get('/{rfq}/edit', [RfqController::class, 'edit'])->name('edit')->middleware('permission:rfq.edit|rfqs:edit');
        Route::put('/{rfq}', [RfqController::class, 'update'])->name('update')->middleware('permission:rfq.edit|rfqs:edit');
        Route::delete('/{rfq}', [RfqController::class, 'destroy'])->name('destroy')->middleware('permission:rfq.delete|rfqs:delete');
        Route::get('/{rfq}/attachments/{attachment}/{mode?}', [RfqController::class, 'attachment'])
            ->name('attachments.show')
            ->where('mode', 'view|download');
        Route::get('/{rfq}/pdf', [RfqController::class, 'pdf'])->name('pdf')->middleware('permission:rfq.view|rfqs:view');
        Route::get('/{rfq}/excel', [RfqController::class, 'excel'])->name('excel')->middleware('permission:rfq.view|rfqs:view');
        Route::get('/{rfq}', [RfqController::class, 'show'])->name('show');
        Route::get('/{rfq}/compare', [VendorQuotationController::class, 'compare'])->name('compare')->middleware('permission:quotation.compare|quotations:compare');
        Route::post('/{rfq}/invite', [RfqController::class, 'invite'])->name('invite')->middleware('permission:rfq.send|rfqs:send|rfq.edit|rfqs:edit');
        Route::post('/{rfq}/send', [RfqController::class, 'send'])->name('send')->middleware('permission:rfq.send|rfqs:send|rfq.edit|rfqs:edit|rfq.create|rfqs:create');
        Route::post('/{rfq}/close', [RfqController::class, 'close'])->name('close')->middleware('permission:rfq.close|rfq.edit|rfqs:edit');
        Route::get('/{rfq}/quotations/create', [VendorQuotationController::class, 'create'])->name('quotations.create')->middleware('permission:quotation.add|quotations:create');
        Route::post('/{rfq}/quotations', [VendorQuotationController::class, 'store'])->name('quotations.store')->middleware('permission:quotation.add|quotations:create');
        Route::get('/{rfq}/quotations/{quotation}', [VendorQuotationController::class, 'show'])->name('quotations.show');
    });

    Route::post('vendor-quotations/{quotation}/select', [VendorQuotationController::class, 'select'])
        ->name('vendor-quotations.select')
        ->middleware('permission:quotation.select-vendor|quotations:select-vendor|quotations.select-vendor|quotation:select-vendor|vendor-quotations.select|vendor-quotations:select|select-vendor|select vendor|quotation.select|quotations:select|rfqs:select-vendor|rfq.select-vendor');
    Route::get('vendor-quotations/{quotation}/negotiation', [VendorQuotationController::class, 'negotiationData'])
        ->name('vendor-quotations.negotiation.show')
        ->middleware('permission:quotation.negotiate|quotation.compare|quotations:compare|quotation.view|quotations:view');
    Route::post('vendor-quotations/{quotation}/negotiation', [VendorQuotationController::class, 'saveNegotiation'])
        ->name('vendor-quotations.negotiation.save')
        ->middleware('permission:quotation.negotiate');

    Route::get('/procurement/items/history', [PurchaseOrderController::class, 'itemHistory'])
        ->name('procurement.items.history');

    Route::prefix('purchase-orders')->name('purchase-orders.')->middleware('permission:purchase-order.view|purchase-orders:view')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create')->middleware('permission:purchase-order.create|purchase-orders:create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store')->middleware('permission:purchase-order.create|purchase-orders:create');
        Route::get('/items/search', [PurchaseOrderController::class, 'searchItems'])->name('items.search')->middleware('permission:purchase-order.create|purchase-orders:create|purchase-order.edit|purchase-orders:edit');
        Route::get('/vendor-rates', [PurchaseOrderController::class, 'searchVendorRates'])->name('vendor-rates')->middleware('permission:purchase-order.create|purchase-orders:create|purchase-order.edit|purchase-orders:edit');
        Route::get('/purchase-requisitions/{purchaseRequisition}/data', [PurchaseOrderController::class, 'prData'])->name('purchase-requisitions.data')->middleware('permission:purchase-order.create|purchase-orders:create|purchase-order.edit|purchase-orders:edit');
        Route::get('/indents/{indent}/data', [PurchaseOrderController::class, 'indentData'])->name('indents.data')->middleware('permission:purchase-order.create|purchase-orders:create|purchase-order.edit|purchase-orders:edit');
        Route::get('/create-from-quotation/{quotation}', [PurchaseOrderController::class, 'createFromQuotation'])->name('create-from-quotation')->middleware('permission:purchase-order.create|purchase-orders:create');
        Route::post('/create-from-quotation/{quotation}', [PurchaseOrderController::class, 'storeFromQuotation'])->name('store-from-quotation')->middleware('permission:purchase-order.create|purchase-orders:create');
        Route::get('/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('print')->middleware('permission:purchase-order.print|purchase-order.view|purchase-orders:view');
        Route::post('/{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->name('submit')->middleware('permission:purchase-order.create|purchase-orders:create|purchase-order.submit');
        // Workflow assignment is enforced in PurchaseOrderApprovalService; route only needs approve/view (not create).
        Route::post('/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('approve')
            ->middleware('permission:purchase-order.approve|purchase-orders:approve|purchase-order.view|purchase-orders:view');
        Route::post('/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('reject')
            ->middleware('permission:purchase-order.reject|purchase-order.approve|purchase-orders:approve|purchase-order.view|purchase-orders:view');
        Route::post('/{purchaseOrder}/send-back', [PurchaseOrderController::class, 'sendBack'])->name('send-back')
            ->middleware('permission:purchase-order.send-back|purchase-order.approve|purchase-orders:approve|purchase-order.view|purchase-orders:view');
        Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit')->middleware('permission:purchase-order.edit|purchase-orders:edit');
        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update')->middleware('permission:purchase-order.edit|purchase-orders:edit');
        Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy')->middleware('permission:purchase-order.delete|purchase-orders:delete');
        Route::post('/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel')->middleware('permission:purchase-order.cancel');
        Route::get('/{purchaseOrder}/payments/summary', [PurchaseOrderController::class, 'paymentSummary'])
            ->name('payments.summary')
            ->middleware('permission:purchase-order.payment|purchase-orders:payment|purchase-order.pay');
        Route::post('/{purchaseOrder}/payments', [PurchaseOrderController::class, 'storePayment'])
            ->name('payments.store')
            ->middleware('permission:purchase-order.payment|purchase-orders:payment|purchase-order.pay');
        Route::get('/payments/{payment}/attachment/{mode?}', [PurchaseOrderController::class, 'paymentAttachment'])
            ->name('payments.attachment')
            ->where('mode', 'view|download');
        Route::post('/{purchaseOrder}/payment-request', [PaymentRequestController::class, 'storeFromPo'])
            ->name('payment-request.store')
            ->middleware('permission:payment.create|payments:create|purchase-order.create|purchase-orders:create|purchase-order.edit|purchase-orders:edit');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
    });

    Route::post('purchase-orders/{purchaseOrder}/payment-request', [PaymentRequestController::class, 'storeFromPo'])
        ->name('payment-request.store')
        ->middleware('permission:payment.create|payments:create|purchase-order.create|purchase-orders:create|purchase-order.edit|purchase-orders:edit');

    Route::prefix('payments')->name('payments.')->middleware('permission:payment.view|payments:view|purchase-order.payment|purchase-orders:payment|purchase-order.view|purchase-orders:view')->group(function () {
        Route::get('/', [PaymentRequestController::class, 'index'])->name('index');
        Route::get('/{paymentRequest}/attachment/{mode?}', [PaymentRequestController::class, 'attachment'])
            ->name('attachment')
            ->where('mode', 'view|download');
        Route::get('/{paymentRequest}', [PaymentRequestController::class, 'show'])->name('show');
        Route::post('/{paymentRequest}/approve', [PaymentRequestController::class, 'approve'])->name('approve')->middleware('permission:payment.approve|payments:approve|purchase-order.payment|purchase-orders:payment|purchase-order.pay');
        Route::post('/{paymentRequest}/reject', [PaymentRequestController::class, 'reject'])->name('reject')->middleware('permission:payment.approve|payments:approve|purchase-order.payment|purchase-orders:payment|purchase-order.pay');
        Route::post('/{paymentRequest}/pay', [PaymentRequestController::class, 'pay'])->name('pay')->middleware('permission:payment.pay|payments:pay|purchase-order.payment|purchase-orders:payment|purchase-order.pay');
    });

    Route::prefix('grns')->name('grns.')->middleware('permission:grn.view|grns:view')->group(function () {
        Route::get('/', [GrnController::class, 'index'])->name('index');
        Route::get('/create/{purchaseOrder}', [GrnController::class, 'create'])->name('create')->middleware('permission:grn.create|grns:create');
        Route::post('/create/{purchaseOrder}', [GrnController::class, 'store'])->name('store')->middleware('permission:grn.create|grns:create');
        Route::get('/{grn}', [GrnController::class, 'show'])->name('show');
        Route::get('/{grn}/documents/{document}/{mode?}', [GrnController::class, 'document'])
            ->name('documents.show')
            ->where('mode', 'view|download');
        Route::post('/{grn}/approve', [GrnController::class, 'approve'])->name('approve')->middleware('permission:grn.approve|grn.inspect|grns:approve');
        Route::post('/{grn}/create-assets', [QualityInspectionController::class, 'createAssets'])->name('create-assets')->middleware('permission:quality-inspection.perform|quality-inspections:perform|grn.approve|grns:approve|asset:create|asset.create');
        Route::post('/{grn}/items/{item}/create-assets', [QualityInspectionController::class, 'createItemAssets'])->name('create-item-assets')->middleware('permission:quality-inspection.perform|quality-inspections:perform|grn.approve|grns:approve|asset:create|asset.create');
    });

    Route::prefix('quality-inspections')->name('quality-inspections.')->middleware('permission:quality-inspection.view|quality-inspections:view|quality-inspection.perform|quality-inspections:perform')->group(function () {
        Route::get('/', [QualityInspectionController::class, 'index'])->name('index');
        Route::get('/{grn}', [QualityInspectionController::class, 'show'])->name('show');
        Route::post('/{grn}/items/{item}/inspect', [QualityInspectionController::class, 'inspectItem'])->name('inspect-item')->middleware('permission:quality-inspection.perform|quality-inspections:perform');
        Route::post('/{grn}/inspect-batch', [QualityInspectionController::class, 'inspectBatch'])->name('inspect-batch')->middleware('permission:quality-inspection.perform|quality-inspections:perform');
        Route::post('/{grn}/create-assets', [QualityInspectionController::class, 'createAssets'])->name('create-assets')->middleware('permission:quality-inspection.perform|quality-inspections:perform|asset:create|asset.create');
        Route::post('/{grn}/items/{item}/create-assets', [QualityInspectionController::class, 'createItemAssets'])->name('create-item-assets')->middleware('permission:quality-inspection.perform|quality-inspections:perform|asset:create|asset.create');
    });

    Route::get('procurement/reports', [ProcurementReportController::class, 'index'])
        ->name('procurement.reports')
        ->middleware('permission:purchase-report.view|procurement.reports:view');
    Route::get('procurement/reports/{report}', [ProcurementReportController::class, 'show'])
        ->name('procurement.reports.show')
        ->middleware('permission:purchase-report.view|procurement.reports:view');

    Route::any('reports/companywiseassets', [ReportsController::class, 'companywiseassets'])->name('reports.companywiseassets');

    Route::any('reports/branchwiseassets', [ReportsController::class, 'branchwiseassets'])->name('reports.branchwiseassets');

    Route::any('reports/brandwiseassets', [ReportsController::class, 'brandwiseassets'])->name('reports.brandwiseassets');

    Route::any('reports/vendorwiseassets', [ReportsController::class, 'vendorwiseassets'])->name('reports.vendorwiseassets');

    Route::any('reports/locationwiseassets', [ReportsController::class, 'locationwiseassets'])->name('reports.locationwiseassets');

    Route::any('reports/categorywiseassets', [ReportsController::class, 'categorywiseassets'])->name('reports.categorywiseassets');

    Route::any('reports/typewiseassets', [ReportsController::class, 'typewiseassets'])->name('reports.typewiseassets');
});
