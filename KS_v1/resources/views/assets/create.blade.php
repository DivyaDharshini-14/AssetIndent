@extends('layouts/contentNavbarLayout')

@section('title', 'Create Asset')

@section('page-style')
<style>
    .form-container {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 2rem;
        border: 1px solid #e3e6f0;
        margin-bottom: 1.5rem;
    }
    /* .form-label-required::after {
        content: " *";
        color: #dc3545;
    } */
    .form-section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    
</style>
@endsection

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Create Asset</span>
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">New Asset</h5>
        <a href="{{route('asset.index')}}" class="btn btn-secondary">Back to list</a>
    </div>

    <div class="card-body">
        <div class="form-container">
            <form id="assetMainForm" action="{{ route('asset.store') }}" method="POST" enctype="multipart/form-data"> 
    @csrf

    {{-- ASSET DETAILS --}}
    <div class="form-section-title">Asset Details</div>
    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
            <input type="text" name="asset_name" id="asset_name" required
                   class="form-control @error('asset_name') is-invalid @enderror"
                   value="{{ old('asset_name') }}"
                   list="asset-name-options"
                   autocomplete="off">
            <datalist id="asset-name-options"></datalist>
            @error('asset_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

         <div class="col-md-4">
            <label class="form-label">Serial Number <span class="text-danger">*</span></label>
            <input type="text" name="serial_no" required
                   class="form-control @error('serial_no') is-invalid @enderror"
                   value="{{ old('serial_no') }}">
            @error('serial_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Asset Code <span class="text-danger">*</span></label>
            <input type="text" name="asset_code" required
                   class="form-control @error('asset_code') is-invalid @enderror"
                   value="{{ old('asset_code', $assetCode) }}">
            @error('asset_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
        <label class="form-label">Asset Structure <span class="text-danger">*</span> </label>
            <select name="asset_structure" id="asset_structure" required class="form-select @error('asset_structure') is-invalid @enderror">
                <option value="">Select Structure</option>
                <option value="single" {{ old('asset_structure','single') == 'single' ? 'selected' : '' }}> Single Asset </option>
                <option value="group" {{ old('asset_structure') == 'group' ? 'selected' : '' }}>  Group Asset </option>
            </select>
            @error('asset_structure') <div class="invalid-feedback">{{ $message }}</div>  @enderror
        </div>

        <div class="col-md-4 d-none" id="itemsCountWrapper">
            <label class="form-label">  Items Count <span class="text-danger">*</span></label>
            <input type="number" min="1" name="items_count" id="items_count" class="form-control @error('items_count') is-invalid @enderror"
                value="{{ old('items_count') }}">
            @error('items_count') <div class="invalid-feedback">{{ $message }}</div>  @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Parent Category <span class="text-danger">*</span></label>
            <select name="category_id" id="category_id" required class="form-select @error('category_id') is-invalid @enderror">
                <option value="">Select Parent Category</option>    
                @foreach ($categories as $category)
                 <option value="{{$category->id}}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{$category->category_name}}</option>   
                @endforeach            
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="sub_category_id" id="sub_category_id" required class="form-select @error('sub_category_id') is-invalid @enderror">
                <option value="">Select Category</option>                
            </select>
            @error('sub_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Asset Type <span class="text-danger">*</span></label>
            <select name="asset_type_id" id="asset_type_id" required
                    class="form-select @error('asset_type_id') is-invalid @enderror">
                <option value="">Select Asset Type</option>                
            </select>
            @error('asset_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

       

        <div class="col-md-4">
         <label class="form-label"> Status <span class="text-danger">*</span> </label>
            <select name="status" required
                    class="form-select @error('status') is-invalid @enderror">
                <option value="">Select Status</option>
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}> Available </option>
                <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}> Assigned </option>                
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
       </div>

       <div class="col-md-4">
         <label class="form-label"> Operational Status <span class="text-danger">*</span> </label>
            <select name="operational_status" required
                    class="form-select @error('operational_status') is-invalid @enderror">
                <option value="">Select Operational Status</option>
                @foreach ($operations_status as $operationStatus)
                    <option value="{{$operationStatus->value}}" {{ old('operational_status') == $operationStatus->value ? 'selected' : '' }}>{{$operationStatus->label()}}</option>
                @endforeach
            </select>
            @error('operational_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
       </div>

       <div class="col-md-4">
    <label class="form-label">
        Condition <span class="text-danger">*</span>
    </label>

    <select name="condition" required
            class="form-select @error('condition') is-invalid @enderror">
        <option value="">Select Condition</option>

        @foreach ($conditions as $condition)
            <option value="{{ $condition->value }}"
                {{ old('condition') == $condition->value ? 'selected' : '' }}>
                {{ $condition->label() }}
            </option>
        @endforeach
    </select>

    @error('condition')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>




    <div class="form-section-title mt-2">Location</div>

<div class="col-md-4">
            <label class="form-label" id="company_label">Company <span class="text-danger">*</span></label>
            <select id="company_id" name="company_id" required
                    class="form-select @error('company_id') is-invalid @enderror">
                <option value="">Select Company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                        {{ ($repopulateForm ?? false) && old('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

<div class="col-md-4">
            <label class="form-label" id="branch_label">Branch <span class="text-danger">*</span></label>
            <select name="branch_id" id="branch_id" required class="form-select @error('branch_id') is-invalid @enderror">
                <option value="">Select Branch</option>
            </select>
            @error('branch_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label" id="location_label">Area</label>
            <select id="location_id" name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                <option value="">Select Area</option>                
            </select>
            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div id="assigned_to_location_row" class="col-12 d-none">
            <div class="row g-3">
            <div class="col-12">
                <small class="text-muted">Assign asset from the location above to the destination below.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">To Company <span class="text-danger">*</span></label>
                <select id="to_company_id" name="to_company_id" class="form-select @error('to_company_id') is-invalid @enderror">
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ ($repopulateForm ?? false) && old('to_company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('to_company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">To Branch <span class="text-danger">*</span></label>
                <select name="to_branch" id="to_branch" class="form-select @error('to_branch') is-invalid @enderror">
                    <option value="">Select Branch</option>
                </select>
                @error('to_branch')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">To Area</label>
                <select id="to_location" name="to_location" class="form-select @error('to_location') is-invalid @enderror">
                    <option value="">Select Area</option>
                </select>
                @error('to_location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            </div>
        </div>

    </div>

    {{-- PURCHASE & WARRANTY --}}
    <div class="form-section-title mt-4">Purchase</div>
    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
            <input type="date" name="purchase_date" required
                   class="form-control @error('purchase_date') is-invalid @enderror"
                   value="{{ old('purchase_date') }}">
            @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Purchase Cost <span class="text-danger">*</span></label>
            <input type="number" step="0.001" name="purchase_cost" required
                   class="form-control @error('purchase_cost') is-invalid @enderror"
                   value="{{ old('purchase_cost') }}">
            @error('purchase_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @php
            $oldSupIdCreate = old('supplier');
            $defaultSupNameCreate = old('supplier_display');
            if ($defaultSupNameCreate === null && $oldSupIdCreate !== null && $oldSupIdCreate !== '') {
                $defaultSupNameCreate = optional(collect($vendors)->first(fn ($v) => (string)$v->id === (string)$oldSupIdCreate))->name;
            }

            $oldBrandIdCreate = old('brand_id');
            $defaultBrandNameCreate = old('brand_display');
            if ($defaultBrandNameCreate === null && $oldBrandIdCreate) {
                $defaultBrandNameCreate = optional(collect($brands)->first(fn ($b) => (string)$b->id === (string)$oldBrandIdCreate))->brand_name;
            }
        @endphp
        <div class="col-md-4">
            <label class="form-label">Supplier <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="text"
                       id="supplier_display"
                       class="form-control @error('supplier') is-invalid @enderror"
                       value="{{ $defaultSupNameCreate ?? '' }}"
                       list="supplier-options"
                       autocomplete="off"
                       placeholder="Type or pick supplier">
                <button type="button" class="btn btn-outline-primary" id="quickCreateSupplierBtn" title="Add supplier">+</button>
            </div>
            <input type="hidden" name="supplier" id="supplier" value="{{ old('supplier') }}">
            <datalist id="supplier-options">
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->name }}"></option>
                @endforeach                
            </datalist>
            @error('supplier')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>  
        
        <div class="col-md-4">
            <label class="form-label">Brand <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="text"
                       id="brand_display"
                       class="form-control @error('brand_id') is-invalid @enderror"
                       value="{{ $defaultBrandNameCreate ?? '' }}"
                       list="brand-options"
                       autocomplete="off"
                       placeholder="Type or pick brand">
                <button type="button" class="btn btn-outline-primary" id="quickCreateBrandBtn" title="Add brand">+</button>
            </div>
            <input type="hidden" name="brand_id" id="brand_id" value="{{ old('brand_id') }}">
            <datalist id="brand-options"></datalist>
            @error('brand_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>  

         <div class="col-md-4">
            <label class="form-label">Invoice </label>
            <input type="text"  name="invoice_details"
                   class="form-control @error('invoice_details') is-invalid @enderror"
                   value="{{ old('invoice_details') }}">
            @error('invoice_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

    </div>

    {{-- DEPRECIATION --}}
    <div class="form-section-title mt-4">Depreciation</div>
    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Depreciation Method <span class="text-danger">*</span></label>
            <select name="depreciation_method" class="form-select" id="depreciation_method">
                @foreach(['no_depreciation','reducing_balance'] as $method)
                    <option value="{{ $method }}"
                        {{ old('depreciation_method') == $method ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ',$method)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 d-none" id="usefulLifeWrapper">
            <label class="form-label">Useful Life (Years) <span class="text-danger">*</span></label>
            <input type="text" name="userful_life"
                   class="form-control"
                   value="{{ old('userful_life') }}">
        </div>

        <div class="col-md-4 d-none" id="salvageWrapper">
            <label class="form-label" id="salvageLabel">Salvage Value <span class="text-danger">*</span></label>
            <input type="number" step="0.01"
                   name="salvage_value"
                   class="form-control"
                   value="{{ old('salvage_value') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Asset Image </label>
            <input class="form-control @error('filename') is-invalid @enderror" type="file" id="filename" name="filename" accept="image/*">              
            @error('filename')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

        <div class="col-md-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

    </div>

    {{-- ACTIONS --}}
    <div class="mt-4 d-flex flex-wrap gap-2 justify-content-end">
        <button type="submit" name="save_and_duplicate" value="1" class="btn btn-success">
            Save &amp; Duplicate
        </button>
        <button type="submit" class="btn btn-primary">Save Asset</button>
        <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>


        </div>
    </div>
</div>

<div class="modal fade" id="quickSupplierModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="quickSupplierForm">
      <div class="modal-header">
        <h5 class="modal-title">Add Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Supplier name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="quick_supplier_name" required>
          <div class="invalid-feedback d-block" id="quick_supplier_name_error"></div>
        </div>
        <div class="mb-0">
          <label class="form-label">Phone <span class="text-muted">(optional)</span></label>
          <input type="text" class="form-control" id="quick_supplier_phone" maxlength="20">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="quickBrandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="quickBrandForm">
      <div class="modal-header">
        <h5 class="modal-title">Add Brand</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-0">
          <label class="form-label">Brand name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="quick_brand_name" required>
          <div class="invalid-feedback d-block" id="quick_brand_name_error"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('page-script')

<script>

const $assetNameInput = $('#asset_name');
const $assetNameOptions = $('#asset-name-options');
let currentAssetNameItems = [];

function renderAssetNameOptions(items) {
    currentAssetNameItems = items || [];
    $assetNameOptions.empty();
    currentAssetNameItems.forEach(function(item) {
        const value = typeof item === 'string' ? item : item.name;
        $assetNameOptions.append(`<option value="${$('<div>').text(value).html()}"></option>`);
    });
}

function checkAutoPopulateFromItemMaster() {
    const val = ($assetNameInput.val() || '').trim().toLowerCase();
    if (!val || !currentAssetNameItems.length) return;
    const match = currentAssetNameItems.find(i => (typeof i !== 'string') && i.name && i.name.toLowerCase() === val);
    if (match) {
        if (match.category_id && $('#category_id').length) {
            $('#category_id').val(match.category_id);
            if (typeof loadSubCategories === 'function') {
                loadSubCategories(match.category_id, match.sub_category_id || null, match.asset_type_id || null);
            } else {
                $('#category_id').trigger('change');
            }
        } else if (match.sub_category_id && typeof loadAssetTypes === 'function') {
            loadAssetTypes(match.sub_category_id, match.asset_type_id || null);
        } else if (match.asset_type_id && $('#asset_type_id').length) {
            $('#asset_type_id').val(match.asset_type_id).trigger('change');
        }
    }
}

function fetchAssetNameSuggestions(term) {
    $.ajax({
        url: "{{ route('asset-names.suggest') }}",
        type: "GET",
        dataType: "json",
        data: { q: term, limit: 50 },
        success: function(res) {
            renderAssetNameOptions((res && res.data) ? res.data : []);
            checkAutoPopulateFromItemMaster();
        }
    });
}

$assetNameInput.on('change select input', function() {
    checkAutoPopulateFromItemMaster();
});


const IS_DUPLICATE_RETURN = @json(!empty($isDuplicateReturn));
const DUPLICATE_SAVE_MESSAGE = @json($duplicateSavedMessage ?? null);
const ASSET_CREATE_DRAFT_KEY = 'empire_asset_create_form_v2';
let saveAssetCreateDraftTimer = null;
let suppressLocationCascade = false;

function saveAssetCreateDraft() {
    try {
        const fields = {};
        $('#assetMainForm').find('input[name], select[name], textarea[name]').each(function () {
            const $el = $(this);
            const name = $el.attr('name');
            if (!name || $el.attr('type') === 'file') return;
            const type = ($el.attr('type') || '').toLowerCase();
            if (type === 'checkbox' || type === 'radio') return;
            fields[name] = $el.val();
        });
        sessionStorage.setItem(ASSET_CREATE_DRAFT_KEY, JSON.stringify({
            v: 2,
            fields: fields,
            supplier_display: ($('#supplier_display').val() || ''),
            brand_display: ($('#brand_display').val() || ''),
        }));
    } catch (e) {}
}

function scheduleSaveAssetCreateDraft() {
    clearTimeout(saveAssetCreateDraftTimer);
    saveAssetCreateDraftTimer = setTimeout(saveAssetCreateDraft, 250);
}

function loadAssetCreateDraft() {
    try {
        const raw = sessionStorage.getItem(ASSET_CREATE_DRAFT_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch (e) {
        return null;
    }
}

function clearAssetCreateDraft() {
    try {
        sessionStorage.removeItem(ASSET_CREATE_DRAFT_KEY);
    } catch (e) {}
}

function isCreatePageReload() {
    try {
        const nav = performance.getEntriesByType('navigation')[0];
        return nav && nav.type === 'reload';
    } catch (e) {
        return false;
    }
}

function restoreCreateFormFromDraft(payload) {
    if (!payload || !payload.fields) {
        return;
    }

    function setNamed(name, value) {
        if (value === undefined || value === null) {
            return;
        }
        const escaped = String(name).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
        $('#assetMainForm').find('[name="' + escaped + '"]').val(value);
    }

    const f = payload.fields;
    const skip = { branch_id: 1, location_id: 1, sub_category_id: 1, asset_type_id: 1, to_branch: 1, to_location: 1 };
    Object.keys(f).forEach(function (name) {
        if (!skip[name]) {
            setNamed(name, f[name]);
        }
    });
    $('#supplier_display').val(payload.supplier_display || '');
    $('#brand_display').val(payload.brand_display || '');

    const companyId = f.company_id || '';
    const branchId = f.branch_id || '';
    const locationId = f.location_id || '';
    const toCompanyId = f.to_company_id || '';
    const toBranchId = f.to_branch || '';
    const toLocationId = f.to_location || '';
    const categoryId = f.category_id || '';
    const subCatId = f.sub_category_id || '';
    const assetTypeId = f.asset_type_id || '';

    let pending = 0;
    if (categoryId) pending++;
    if (companyId) pending++;
    if (toCompanyId && isAssignedStatus()) pending++;

    function finishDraftRestore() {
        syncLocationLabels();
        rebuildSupplierDatalist();
        rebuildBrandDatalist();
        syncSupplierHidden();
        syncBrandHidden();
        toggleAssetStructure();
        toggleDepreciationFields();
        fetchAssetNameSuggestions($('#asset_name').val() || '');
    }

    if (pending === 0) {
        finishDraftRestore();
        return;
    }

    if (categoryId) {
        loadSubCategories(categoryId, subCatId || null, assetTypeId || null, function () {
            pending--;
            if (pending === 0) finishDraftRestore();
        });
    }

    if (companyId) {
        loadBranches(companyId, branchId || null, locationId || null, function () {
            pending--;
            if (pending === 0) finishDraftRestore();
        });
    }

    if (toCompanyId && isAssignedStatus()) {
        loadToBranches(toCompanyId, toBranchId || null, toLocationId || null, function () {
            pending--;
            if (pending === 0) finishDraftRestore();
        });
    }
}

let SUPPLIERS_DATA = @json($vendors->map(fn ($v) => ['id' => (int)$v->id, 'name' => (string)$v->name])->values());
let BRANDS_DATA = @json($brands->map(fn ($b) => ['id' => (int)$b->id, 'name' => (string)$b->brand_name])->values());

function toastSuccessAssetForm(text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'success', title: 'Success', text: text, timer: 1800, showConfirmButton: false });
    } else {
        alert(text);
    }
}

function rebuildSupplierDatalist() {
    const seen = {};
    const $opts = $('#supplier-options');
    $opts.empty();
    SUPPLIERS_DATA.forEach(function (v) {
        const kk = v.name.trim().toLowerCase();
        if (seen[kk]) {
            return;
        }
        seen[kk] = true;
        $opts.append($('<option></option>').attr('value', v.name));
    });
}

function registerSupplier(row) {
    if (!row || !row.id) {
        return false;
    }
    const id = parseInt(row.id, 10);
    const name = (row.name || '').trim();
    if (!name) {
        return false;
    }
    const ix = SUPPLIERS_DATA.findIndex(function (v) {
        return String(v.id) === String(id);
    });
    if (ix >= 0) {
        SUPPLIERS_DATA[ix].name = name;
    } else {
        SUPPLIERS_DATA.push({ id: id, name: name });
    }
    rebuildSupplierDatalist();
    $('#supplier').val(id);
    $('#supplier_display').val(name);
    saveAssetCreateDraft();
    return true;
}

function syncSupplierHidden() {
    const raw = ($('#supplier_display').val() || '').trim();
    $('#supplier').val('');
    if (!raw) {
        return false;
    }
    const k = raw.toLowerCase();
    const hits = SUPPLIERS_DATA.filter(function (v) {
        return v.name.trim().toLowerCase() === k;
    });
    if (hits.length !== 1) {
        return false;
    }
    $('#supplier').val(hits[0].id);
    return true;
}

function rebuildBrandDatalist() {
    const seen = {};
    const $opts = $('#brand-options');
    $opts.empty();
    BRANDS_DATA.forEach(function (b) {
        const kk = b.name.trim().toLowerCase();
        if (seen[kk]) {
            return;
        }
        seen[kk] = true;
        $opts.append($('<option></option>').attr('value', b.name));
    });
}

function registerBrand(row) {
    if (!row || !row.id) {
        return false;
    }
    const id = parseInt(row.id, 10);
    const name = (row.brand_name || row.name || '').trim();
    if (!name) {
        return false;
    }
    const ix = BRANDS_DATA.findIndex(function (b) {
        return String(b.id) === String(id);
    });
    if (ix >= 0) {
        BRANDS_DATA[ix].name = name;
    } else {
        BRANDS_DATA.push({ id: id, name: name });
    }
    rebuildBrandDatalist();
    $('#brand_id').val(id);
    $('#brand_display').val(name);
    saveAssetCreateDraft();
    return true;
}

function syncBrandHidden() {
    const raw = ($('#brand_display').val() || '').trim();
    $('#brand_id').val('');
    if (!raw) {
        return false;
    }
    const k = raw.toLowerCase();
    const hits = BRANDS_DATA.filter(function (b) {
        return b.name.trim().toLowerCase() === k;
    });
    if (hits.length !== 1) {
        return false;
    }
    $('#brand_id').val(hits[0].id);
    return true;
}

function postBrandQuick(brandName, done) {
    $.ajax({
        url: "{{ route('brand.quick-store') }}",
        type: 'POST',
        dataType: 'json',
        data: {
            _token: "{{ csrf_token() }}",
            brand_name: brandName,
        },
        success: function (res) {
            if (res && res.data) {
                registerBrand(res.data);
            }
            toastSuccessAssetForm((res && res.message) ? res.message : 'Brand saved.');
            if (quickBrandModal) {
                quickBrandModal.hide();
            }
            done && done(null, res);
        },
        error: function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message)
                || (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.brand_name && xhr.responseJSON.errors.brand_name[0])
                || 'Unable to save brand.';
            done && done(msg, xhr);
        }
    });
}

function postVendorQuick(name, phone, done) {
    $.ajax({
        url: "{{ route('vendor.quick-store') }}",
        type: 'POST',
        dataType: 'json',
        data: {
            _token: "{{ csrf_token() }}",
            name: name,
            phone_number: phone || ''
        },
        success: function (res) {
            if (res && res.data) {
                registerSupplier(res.data);
            }
            toastSuccessAssetForm((res && res.message) ? res.message : 'Supplier saved.');
            if (quickSupplierModal) {
                quickSupplierModal.hide();
            }
            done && done(null, res);
        },
        error: function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message)
                || (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name && xhr.responseJSON.errors.name[0])
                || 'Unable to save supplier.';
            done && done(msg, xhr);
        }
    });
}

$assetNameInput.on('focus input', function() {
    fetchAssetNameSuggestions($(this).val() || '');
});

var quickSupplierModal = null;
if (typeof bootstrap !== 'undefined' && document.getElementById('quickSupplierModal')) {
    quickSupplierModal = new bootstrap.Modal(document.getElementById('quickSupplierModal'));
}

function showQuickSupplierModalFn() {
    if (quickSupplierModal) {
        quickSupplierModal.show();
    } else {
        $('#quickSupplierModal').show();
    }
}

var quickBrandModal = null;
if (typeof bootstrap !== 'undefined' && document.getElementById('quickBrandModal')) {
    quickBrandModal = new bootstrap.Modal(document.getElementById('quickBrandModal'));
}

function showQuickBrandModalFn() {
    if (quickBrandModal) {
        quickBrandModal.show();
    } else {
        $('#quickBrandModal').show();
    }
}

$('#supplier_display').on('input', function () {
    $('#supplier').val('');
});

$('#supplier_display').on('change blur', function () {
    syncSupplierHidden();
});

$('#brand_display').on('input', function () {
    $('#brand_id').val('');
});

$('#brand_display').on('change blur', function () {
    syncBrandHidden();
});

$('#quickCreateSupplierBtn').on('click', function() {
    $('#quick_supplier_name_error').text('');
    const typed = ($('#supplier_display').val() || '').trim();

    if (typed !== '') {
        postVendorQuick(typed, '', function (err) {
            if (err) {
                $('#quick_supplier_name').val(typed);
                $('#quick_supplier_phone').val('');
                $('#quick_supplier_name_error').text(typeof err === 'string' ? err : 'Unable to save supplier.');
                showQuickSupplierModalFn();
            }
        });
        return;
    }

    $('#quick_supplier_name').val('');
    $('#quick_supplier_phone').val('');
    showQuickSupplierModalFn();
});

$('#quickSupplierForm').on('submit', function(e) {
    e.preventDefault();
    const name = ($('#quick_supplier_name').val() || '').trim();
    $('#quick_supplier_name_error').text('');
    if (!name) {
        $('#quick_supplier_name_error').text('Supplier name is required.');
        return;
    }

    const phone = ($('#quick_supplier_phone').val() || '').trim();
    postVendorQuick(name, phone, function (err) {
        if (err) {
            $('#quick_supplier_name_error').text(typeof err === 'string' ? err : 'Unable to save supplier.');
        }
    });
});

$('#quickCreateBrandBtn').on('click', function() {
    $('#quick_brand_name_error').text('');
    const typed = ($('#brand_display').val() || '').trim();

    if (typed !== '') {
        postBrandQuick(typed, function (err) {
            if (err) {
                $('#quick_brand_name').val(typed);
                $('#quick_brand_name_error').text(typeof err === 'string' ? err : 'Unable to save brand.');
                showQuickBrandModalFn();
            }
        });
        return;
    }

    $('#quick_brand_name').val('');
    showQuickBrandModalFn();
});

$('#quickBrandForm').on('submit', function(e) {
    e.preventDefault();
    const name = ($('#quick_brand_name').val() || '').trim();
    $('#quick_brand_name_error').text('');
    if (!name) {
        $('#quick_brand_name_error').text('Brand name is required.');
        return;
    }

    postBrandQuick(name, function (err) {
        if (err) {
            $('#quick_brand_name_error').text(typeof err === 'string' ? err : 'Unable to save brand.');
        }
    });
});

    $('.select2').select2({
    placeholder: "Select",
    allowClear: true,
    closeOnSelect: true, 
    width: '100%'
});


    const REPOPULATE_FORM = @json($repopulateForm ?? false);
    const OLD_CATEGORY = @json(($repopulateForm ?? false) ? old('category_id') : '');
    const OLD_SUBCATEGORY = @json(($repopulateForm ?? false) ? old('sub_category_id') : '');
    const OLD_ASSETTYPE = @json(($repopulateForm ?? false) ? old('asset_type_id') : '');
    const OLD_BRANCH = @json(($repopulateForm ?? false) ? old('branch_id') : '');
    const OLD_LOCATION = @json(($repopulateForm ?? false) ? old('location_id') : '');
    const OLD_TO_BRANCH = @json(($repopulateForm ?? false) ? old('to_branch') : '');
    const OLD_TO_LOCATION = @json(($repopulateForm ?? false) ? old('to_location') : '');
    const OLD_COMPANY = @json(($repopulateForm ?? false) ? old('company_id') : '');
    const OLD_TO_COMPANY = @json(($repopulateForm ?? false) ? old('to_company_id') : '');
    const OLD_STATUS = @json(($repopulateForm ?? false) ? old('status') : '');
    const WAREHOUSE_COMPANY_ID = {{ (int) ($warehouseCompanyId ?? 0) }};
    const WAREHOUSE_BRANCH_ID = {{ (int) ($warehouseBranchId ?? 0) }};
    const WAREHOUSE_LOCATION_ID = "{{ $warehouseLocationId ?? '' }}";

    function isAssignedStatus() {
        return ($('select[name="status"]').val() || '') === 'assigned';
    }

    function syncLocationLabels() {
        const assigned = isAssignedStatus();
        $('#company_label').html(assigned ? 'From Company <span class="text-danger">*</span>' : 'Company <span class="text-danger">*</span>');
        $('#branch_label').html(assigned ? 'From Branch <span class="text-danger">*</span>' : 'Branch <span class="text-danger">*</span>');
        $('#location_label').text(assigned ? 'From Area' : 'Area');
        $('#assigned_to_location_row').toggleClass('d-none', !assigned);
        $('#to_company_id').prop('required', assigned);
        $('#to_branch').prop('required', assigned);
    }

    function loadToBranches(companyId, selectedBranch = null, selectedLocation = null, onComplete = null) {
        const $branch = $('#to_branch');
        $branch.html('<option value="">Loading...</option>');

        if (!companyId) {
            $branch.html('<option value="">Select Branch</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }

        $.get(
            "{{ route('ajax.getbranch.bycompany', ':id') }}".replace(':id', companyId),
            function (res) {
                $branch.empty().append('<option value="">Select Branch</option>');
                if (res.data && res.data.length > 0) {
                    $.each(res.data, function (_, value) {
                        const sel = (selectedBranch != null && String(selectedBranch) === String(value.id)) ? 'selected' : '';
                        $branch.append(`<option value="${value.id}" ${sel}>${value.name}</option>`);
                    });
                }
                if (selectedBranch) {
                    loadToLocations(String(selectedBranch), selectedLocation, onComplete);
                } else if (typeof onComplete === 'function') {
                    onComplete();
                }
            }
        ).fail(function () {
            $branch.html('<option value="">Failed to load branches</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
        });
    }

    function loadToLocations(branchId, selectedLocation = null, onComplete = null) {
        const $location = $('#to_location');
        $location.html('<option value="">Loading...</option>');

        if (!branchId) {
            $location.html('<option value="">Select Area</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }

        $.get(
            "{{ route('ajax.getlocation.bybranch', ':id') }}".replace(':id', branchId),
            function (res) {
                $location.empty().append('<option value="">Select Area</option>');
                if (res.data && res.data.length > 0) {
                    $.each(res.data, function (_, value) {
                        const sel = (selectedLocation != null && String(selectedLocation) === String(value.id)) ? 'selected' : '';
                        $location.append(`<option value="${value.id}" ${sel}>${value.location_name}</option>`);
                    });
                }
                if (typeof onComplete === 'function') {
                    onComplete();
                }
            }
        ).fail(function () {
            $location.html('<option value="">Select Area</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
        });
    }

    function clearFromLocationFields() {
        $('#company_id').val('');
        $('#branch_id').html('<option value="">Select Branch</option>');
        $('#location_id').html('<option value="">Select Area</option>');
    }

    function clearToLocationFields() {
        $('#to_company_id').val('');
        $('#to_branch').html('<option value="">Select Branch</option>');
        $('#to_location').html('<option value="">Select Area</option>');
    }

    function setCompanyIdSilently(companyId) {
        suppressLocationCascade = true;
        $('#company_id').val(companyId ? String(companyId) : '');
        suppressLocationCascade = false;
    }

    function prefillWarehouseFromLocation(onComplete) {
        const defaultCompanyId = 1;
        const defaultBranchId = 1;
        setCompanyIdSilently(String(defaultCompanyId));
        loadBranches(defaultCompanyId, defaultBranchId, null, onComplete);
    }

            function loadBranches(companyId, selectedId = null, selectedLocationId = null, onComplete = null) {
            const branchDropdown = $('#branch_id');
            branchDropdown.html('<option value="">Loading...</option>');

            if (!companyId) {
                branchDropdown.html('<option value="">Select Branch</option>');
                if (typeof onComplete === 'function') {
                    onComplete();
                }
                return;
            }

            $.get(
                "{{ route('ajax.getbranch.bycompany', ':id') }}".replace(':id', companyId),
                function (res) {
                    branchDropdown.empty().append('<option value="">Select Branch</option>');
                    if (res.data && res.data.length > 0) {
                        $.each(res.data, function (_, value) {
                            const sel = (selectedId != null && String(selectedId) === String(value.id)) ? 'selected' : '';
                            branchDropdown.append(
                                `<option value="${value.id}" ${sel}>${value.name}</option>`
                            );
                        });
                    } else {
                        branchDropdown.append('<option value="">No branches found</option>');
                    }
                    if (selectedId) {
                        loadLocations(String(selectedId), selectedLocationId, onComplete);
                    } else if (typeof onComplete === 'function') {
                        onComplete();
                    }
                }
            ).fail(function () {
                branchDropdown.html('<option value="">Failed to load branches</option>');
                if (typeof onComplete === 'function') {
                    onComplete();
                }
            });
        }

        $('#company_id').on('change', function () {
            if (suppressLocationCascade) {
                return;
            }
            $('#location_id').empty().append('<option value="">Select Area</option>');
            loadBranches(this.value, null, null);
        });

        $('#to_company_id').on('change', function () {
            if (suppressLocationCascade) {
                return;
            }
            $('#to_location').empty().append('<option value="">Select Area</option>');
            loadToBranches(this.value, null, null);
        });

        $('#to_branch').on('change', function () {
            loadToLocations(this.value, null);
        });

        $('select[name="status"]').on('change', function () {
            const wasAssigned = $('#assigned_to_location_row').hasClass('d-none') === false;
            syncLocationLabels();
            if (isAssignedStatus()) {
                if (!wasAssigned) {
                    if (REPOPULATE_FORM && OLD_COMPANY) {
                        setCompanyIdSilently(OLD_COMPANY);
                        loadBranches(OLD_COMPANY, OLD_BRANCH || null, OLD_LOCATION || null);
                    } else if (!REPOPULATE_FORM) {
                        prefillWarehouseFromLocation();
                    }
                }
                const toCompanyId = $('#to_company_id').val() || (REPOPULATE_FORM ? OLD_TO_COMPANY : '');
                if (toCompanyId) {
                    if (!$('#to_company_id').val()) {
                        suppressLocationCascade = true;
                        $('#to_company_id').val(String(toCompanyId));
                        suppressLocationCascade = false;
                    }
                    const toBranch = REPOPULATE_FORM ? (OLD_TO_BRANCH || null) : null;
                    const toLocation = REPOPULATE_FORM ? (OLD_TO_LOCATION || null) : null;
                    loadToBranches(toCompanyId, toBranch, toLocation);
                }
            } else {
                if (wasAssigned && !REPOPULATE_FORM) {
                    clearFromLocationFields();
                }
                clearToLocationFields();
            }
        });



    function loadSubCategories(categoryId, selectedId = null, selectedAssetTypeId = null, onComplete = null) {
        const subCategoryDropDown = $('#sub_category_id');
        subCategoryDropDown.html('<option>Loading...</option>');

        if (!categoryId) {
            subCategoryDropDown.html('<option value="">Select Category</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }

        $.get("{{ route('ajax.getsubcategory.bycategory', ':id') }}".replace(':id', categoryId), function (res) {
            subCategoryDropDown.empty().append('<option value="">Select Category</option>');

            $.each(res.data, function (_, value) {
                let selected = selectedId == value.id ? 'selected' : '';
                subCategoryDropDown.append(
                    `<option value="${value.id}" ${selected}>${value.category_name}</option>`
                );
            });

            if (selectedId) {
                loadAssetTypes(selectedId, selectedAssetTypeId, onComplete);
            } else if (typeof onComplete === 'function') {
                onComplete();
            }
        }).fail(function () {
            subCategoryDropDown.html('<option value="">Select Category</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
        });
    }

    $('#category_id').on('change', function () {
        loadSubCategories(this.value);
    });

    function loadAssetTypes(subId, selectedId = null, onComplete = null) {
    const assetTypeDropDown = $('#asset_type_id');
    assetTypeDropDown.html('<option>Loading...</option>');

    if (!subId) {
        assetTypeDropDown.html('<option value="">Select Asset Type</option>');
        if (typeof onComplete === 'function') {
            onComplete();
        }
        return;
    }

    $.get("{{ route('ajax.getassettype.byassetcategory', ':id') }}".replace(':id', subId), function (res) {
        assetTypeDropDown.empty().append('<option value="">Select Asset Type</option>');

        $.each(res.data, function (_, value) {
            let selected = selectedId == value.id ? 'selected' : '';
            assetTypeDropDown.append(
                `<option value="${value.id}" ${selected}>${value.asset_type_name}</option>`
            );
                });
        if (typeof onComplete === 'function') {
            onComplete();
        }
    }).fail(function () {
        assetTypeDropDown.html('<option value="">Select Asset Type</option>');
        if (typeof onComplete === 'function') {
            onComplete();
        }
            });
        }

        $('#sub_category_id').on('change', function () {
            loadAssetTypes(this.value);
        });


        function loadLocations(branchId, selectedId = null, onComplete = null) {
        const locationSelect = $('#location_id');
        locationSelect.html('<option>Loading...</option>');

        if (!branchId) {
            locationSelect.html('<option value="">Select Location</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }

        $.get("{{ route('ajax.getlocation.bybranch', ':id') }}".replace(':id', branchId), function (res) {
            locationSelect.empty().append('<option value="">Select Location</option>');

            $.each(res.data, function (_, item) {
                let selected = selectedId == item.id ? 'selected' : '';
                    locationSelect.append(
                        `<option value="${item.id}" ${selected}>${item.location_name}</option>`
                    );
                });
            if (typeof onComplete === 'function') {
                onComplete();
            }
        }).fail(function () {
            locationSelect.html('<option value="">Select Location</option>');
            if (typeof onComplete === 'function') {
                onComplete();
            }
            });
        }

        $('#branch_id').on('change', function () {
            loadLocations($(this).val(), null);
        });

    function toggleAssetStructure() {
    let structure = $('#asset_structure').val();

        if (structure === 'group') {
            $('#itemsCountWrapper').removeClass('d-none');
            $('#items_count').prop('required', true);
        } else {
            $('#itemsCountWrapper').addClass('d-none');
            $('#items_count').prop('required', false).val('');
        }
    }

    $('#asset_structure').on('change', toggleAssetStructure);

    function toggleDepreciationFields(){

        let method = $('#depreciation_method').val();

        // Reset everything
        $('#usefulLifeWrapper, #salvageWrapper').addClass('d-none');
        $('#useful_life, #salvage_value').prop('required', false);

        if(method === 'straight_line'){
            $('#usefulLifeWrapper').removeClass('d-none');
            $('#salvageWrapper').removeClass('d-none');

            $('#salvageLabel').html('Salvage Value <span class="text-danger">*</span>');

            $('#useful_life, #salvage_value').prop('required', true);
        }
        else if(method === 'reducing_balance'){
            $('#usefulLifeWrapper').addClass('d-none');
            $('#salvageWrapper').removeClass('d-none');

            $('#salvageLabel').html('Rate of Depreciation (%) <span class="text-danger">*</span>');

            $('#salvage_value').prop('required', true);
        }

    }

    $('#depreciation_method').on('change', toggleDepreciationFields);

    

$(document).ready(function () {   

    rebuildSupplierDatalist();
    rebuildBrandDatalist();

    function initCreateFormWithoutDraft(options) {
        const opts = options || {};
        const onReady = typeof opts.onReady === 'function' ? opts.onReady : null;
        let pending = 0;

        function trackComplete(callback) {
            pending++;
            return function () {
                if (typeof callback === 'function') {
                    callback.apply(this, arguments);
                }
                pending--;
                if (pending <= 0 && onReady) {
                    onReady();
                }
            };
        }

        if (REPOPULATE_FORM && OLD_STATUS) {
            $('select[name="status"]').val(OLD_STATUS);
        }

        syncLocationLabels();
        toggleAssetStructure();
        toggleDepreciationFields();
        if (opts.clearEmptyLocations && !isAssignedStatus()) {
            clearFromLocationFields();
            clearToLocationFields();
        }

        const companyForLoad = REPOPULATE_FORM ? (OLD_COMPANY || $('#company_id').val()) : '';
        if (companyForLoad) {
            if (REPOPULATE_FORM && OLD_COMPANY && String($('#company_id').val()) !== String(OLD_COMPANY)) {
                setCompanyIdSilently(OLD_COMPANY);
            }
            loadBranches(companyForLoad, OLD_BRANCH || null, OLD_LOCATION || null, trackComplete(null));
        }

        const toCompanyForLoad = REPOPULATE_FORM ? (OLD_TO_COMPANY || $('#to_company_id').val()) : '';
        if (isAssignedStatus() && toCompanyForLoad) {
            if (REPOPULATE_FORM && OLD_TO_COMPANY && String($('#to_company_id').val()) !== String(OLD_TO_COMPANY)) {
                suppressLocationCascade = true;
                $('#to_company_id').val(String(OLD_TO_COMPANY));
                suppressLocationCascade = false;
            }
            loadToBranches(toCompanyForLoad, OLD_TO_BRANCH || null, OLD_TO_LOCATION || null, trackComplete(null));
        }

        syncSupplierHidden();
        syncBrandHidden();
        if (OLD_CATEGORY) {
            pending++;
            loadSubCategories(OLD_CATEGORY, OLD_SUBCATEGORY, OLD_ASSETTYPE, trackComplete(null));
        }
        fetchAssetNameSuggestions($('#asset_name').val() || '');

        if (pending === 0 && onReady) {
            onReady();
        }
    }

    const pageReload = isCreatePageReload();
    const draftSnap = loadAssetCreateDraft();
    const hasDraft = draftSnap && draftSnap.fields && Object.keys(draftSnap.fields).length > 0;

    if (REPOPULATE_FORM) {
        clearAssetCreateDraft();
        initCreateFormWithoutDraft({
            clearEmptyLocations: false,
            onReady: function () {
                saveAssetCreateDraft();
            },
        });
        if (DUPLICATE_SAVE_MESSAGE) {
            toastSuccessAssetForm(DUPLICATE_SAVE_MESSAGE);
            $('input[name="asset_code"]').trigger('focus');
        }
    } else if (pageReload && hasDraft) {
        restoreCreateFormFromDraft(draftSnap);
    } else {
        clearAssetCreateDraft();
        initCreateFormWithoutDraft({ clearEmptyLocations: true });
    }

    $('#assetMainForm').on('input change', 'input, select, textarea', function () {
        scheduleSaveAssetCreateDraft();
    });
    $('#supplier_display, #brand_display').on('input change blur', scheduleSaveAssetCreateDraft);
    $assetNameInput.on('input change blur', scheduleSaveAssetCreateDraft);
    $(window).on('beforeunload', saveAssetCreateDraft);

    $('#assetMainForm').on('submit', function (e) {
        syncSupplierHidden();
        syncBrandHidden();
        if (!$('#supplier').val()) {
            e.preventDefault();
            alert('Choose a supplier from the suggestions (matching name exactly), or type a new name and click +.');
            $('#supplier_display').trigger('focus');
            return false;
        }
        if (!$('#brand_id').val()) {
            e.preventDefault();
            alert('Choose a brand from the suggestions (matching name exactly), or type a new name and click +.');
            $('#brand_display').trigger('focus');
            return false;
        }
        if (!$('#branch_id').val()) {
            e.preventDefault();
            alert(isAssignedStatus() ? 'Select a from branch.' : 'Select a branch for the selected company.');
            $('#branch_id').trigger('focus');
            return false;
        }
        if (isAssignedStatus() && !$('#to_branch').val()) {
            e.preventDefault();
            alert('Select a to branch for assignment.');
            $('#to_branch').trigger('focus');
            return false;
        }
        if (isAssignedStatus() && $('#to_branch').val() === $('#branch_id').val()) {
            e.preventDefault();
            alert('To branch must be different from from branch.');
            $('#to_branch').trigger('focus');
            return false;
        }
        const submitter = e.originalEvent && e.originalEvent.submitter;
        const isSaveAndDuplicate = submitter && submitter.name === 'save_and_duplicate';
        if (!isSaveAndDuplicate) {
            clearAssetCreateDraft();
        }
        return true;
    });

});

</script>

@endsection
