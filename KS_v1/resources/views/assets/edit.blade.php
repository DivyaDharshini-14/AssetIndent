@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Asset')

@section('page-style')
<style>
    .form-container {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 2rem;
        border: 1px solid #e3e6f0;
        margin-bottom: 1.5rem;
    }
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
    <span class="text-muted fw-light">Edit Asset</span>
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Edit Asset</h5>
        <a href="{{ route('asset.index') }}" class="btn btn-secondary">Back to list</a>
    </div>

    <div class="card-body">
        <div class="form-container">

<form id="assetEditForm" action="{{ route('asset.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ================= ASSET DETAILS ================= --}}
    <div class="form-section-title">Asset Details</div>
    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
            <input type="text" name="asset_name" required
                   class="form-control @error('asset_name') is-invalid @enderror"
                   value="{{ old('asset_name', $asset->asset_name) }}">
            @error('asset_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

 <div class="col-md-4">
            <label class="form-label">Serial Number <span class="text-danger">*</span></label>
            <input type="text" name="serial_no" required
                   class="form-control @error('serial_no') is-invalid @enderror"
                   value="{{ old('serial_no', $asset->serial_no) }}">
            @error('serial_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Asset Code <span class="text-danger">*</span></label>
            <input type="text" name="asset_code" required
                   class="form-control @error('asset_code') is-invalid @enderror"
                   value="{{ old('asset_code', $asset->asset_code) }}">
            @error('asset_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
        <label class="form-label">Asset Structure <span class="text-danger">*</span>  </label>
        <select name="asset_structure" id="asset_structure" required  class="form-select @error('asset_structure') is-invalid @enderror">
            <option value="">Select Structure</option>
            <option value="single" {{ old('asset_structure', $asset->asset_structure) == 'single' ? 'selected' : '' }}> Single Asset  </option>
            <option value="group"  {{ old('asset_structure', $asset->asset_structure) == 'group' ? 'selected' : '' }}> Group Asset </option>
        </select>
        @error('asset_structure')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4 d-none" id="itemsCountWrapper">
        <label class="form-label"> Items Count <span class="text-danger">*</span> </label>
        <input type="number" min="1" name="items_count" id="items_count" class="form-control @error('items_count') is-invalid @enderror" value="{{ old('items_count', $asset->items_count) }}">
        @error('items_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
        <label class="form-label"> Parent Category <span class="text-danger">*</span> </label>
        <select name="category_id" id="category_id" required class="form-select @error('category_id') is-invalid @enderror">
            <option value="">Select Parent Category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->category_name }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
        <label class="form-label"> Category <span class="text-danger">*</span>  </label>
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
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" required
                    class="form-select @error('status') is-invalid @enderror">
                <option value="">Select Status </option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}"
                        {{ old('status', $asset->status?->value) == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

         <div class="col-md-4">
         <label class="form-label"> Operational Status <span class="text-danger">*</span> </label>
            <select name="operational_status" required
                    class="form-select @error('operational_status') is-invalid @enderror">
                <option value="">Select Operational Status</option>
                @foreach ($operations_status as $operationStatus)
                    <option value="{{$operationStatus->value}}" {{ old('operational_status', $asset->operational_status?->value) == $operationStatus->value ? 'selected' : '' }}>{{$operationStatus->label()}}</option>
                @endforeach
            </select>
            @error('operational_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
       </div>

        <div class="col-md-4">
            <label class="form-label">Condition <span class="text-danger">*</span></label>
            <select name="condition" required
                    class="form-select @error('condition') is-invalid @enderror">
                <option value="">Select Condition</option>
                @foreach ($conditions as $condition)
                    <option value="{{ $condition->value }}"
                        {{ old('condition', $asset->condition?->value) == $condition->value ? 'selected' : '' }}>
                        {{ $condition->label() }}
                    </option>
                @endforeach
            </select>
            @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Company <span class="text-danger">*</span></label>
            <select id="company_id" name="company_id" required
                    class="form-select @error('company_id') is-invalid @enderror">
                <option value="">Select Company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                        {{ old('company_id',$asset->company_id) == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @php
            $__supplierIdEdit = old('supplier', $asset->supplier);
            $__supplierNameEdit = old('supplier_display');
            if ($__supplierNameEdit === null && $__supplierIdEdit !== null && $__supplierIdEdit !== '') {
                $__supplierNameEdit = optional(collect($vendors)->first(fn ($v) => (string)$v->id === (string)$__supplierIdEdit))->name;
            }

            $__brandIdEdit = old('brand_id', $asset->brand_id);
            $__brandNameEdit = old('brand_display');
            if ($__brandNameEdit === null && $__brandIdEdit) {
                $__brandNameEdit = optional(collect($brands)->first(fn ($b) => (string)$b->id === (string)$__brandIdEdit))->brand_name
                    ?? optional($asset->brand)->brand_name;
            }
        @endphp

        <div class="col-md-4">
            <label class="form-label">Branch <span class="text-danger">*</span></label>
            <select name="branch_id" id="branch_id" required class="form-select @error('branch_id') is-invalid @enderror">
                <option value="">Select Branch</option>
            </select>
            @error('branch_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Area</label>
            <select id="location_id" name="location_id"
                    class="form-select @error('location_id') is-invalid @enderror">
                <option value="">Select Area</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}"
                        {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>
                        {{ $location->location_name }}
                    </option>
                @endforeach
            </select>
            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

    </div>

    {{-- ================= PURCHASE & WARRANTY ================= --}}
    <div class="form-section-title mt-4">Purchase</div>
    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
            <input type="date" name="purchase_date" required
                   class="form-control @error('purchase_date') is-invalid @enderror"
                   value="{{ old('purchase_date', optional($asset->purchase_date)->format('Y-m-d')) }}">
            @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Purchase Cost <span class="text-danger">*</span></label>
            <input type="number" step="0.001" name="purchase_cost" required
                   class="form-control @error('purchase_cost') is-invalid @enderror"
                   value="{{ old('purchase_cost', $asset->purchase_cost) }}">
            @error('purchase_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Supplier <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="text"
                       id="supplier_display"
                       class="form-control @error('supplier') is-invalid @enderror"
                       value="{{ $__supplierNameEdit ?? '' }}"
                       list="supplier-options"
                       autocomplete="off"
                       placeholder="Type or pick supplier">
                <button type="button" class="btn btn-outline-primary" id="quickCreateSupplierBtn" title="Add supplier">+</button>
            </div>
            <input type="hidden" name="supplier" id="supplier" value="{{ old('supplier', $asset->supplier) }}">
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
                       value="{{ $__brandNameEdit ?? '' }}"
                       list="brand-options"
                       autocomplete="off"
                       placeholder="Type or pick brand">
                <button type="button" class="btn btn-outline-primary" id="quickCreateBrandBtn" title="Add brand">+</button>
            </div>
            <input type="hidden" name="brand_id" id="brand_id" value="{{ old('brand_id', $asset->brand_id) }}">
            <datalist id="brand-options"></datalist>
            @error('brand_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>  

         <div class="col-md-4">
            <label class="form-label">Invoice </label>
            <input type="text"  name="invoice_details"
                   class="form-control @error('invoice_details') is-invalid @enderror"
                   value="{{ old('invoice_details',$asset->invoice_details) }}">
            @error('invoice_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>       

    </div>

    {{-- ================= DEPRECIATION ================= --}}
    <div class="form-section-title mt-4">Depreciation</div>
    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Depreciation Method</label>
            <select name="depreciation_method" class="form-select" id="depreciation_method">
                @foreach(['no_depreciation','reducing_balance'] as $method)
                    <option value="{{ $method }}"
                        {{ old('depreciation_method', $asset->depreciation_method) == $method ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ',$method)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 d-none" id="usefulLifeWrapper">
            <label class="form-label">Useful Life (Years)</label>
            <input type="text" name="userful_life"
                   class="form-control"
                   value="{{ old('userful_life', $asset->userful_life) }}">
        </div>

        <div class="col-md-4 d-none" id="salvageWrapper">
            <label class="form-label" id="salvageLabel">Salvage Value</label>
            <input type="number" step="0.01"
                   name="salvage_value"
                   class="form-control"
                   value="{{ old('salvage_value', $asset->salvage_value) }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Asset Image </label>
            <input class="form-control @error('filename') is-invalid @enderror" type="file" id="filename" name="filename" accept="image/*">              
            @error('filename')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="mt-2">
                @if ($asset->filename)
                    <img src="{{ $asset->doc_url }}" alt="" width="100">
                @endif
            </div>
          </div>

        <div class="col-md-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">
{{ old('description', $asset->description) }}
            </textarea>
        </div>

    </div>

    {{-- ================= ACTIONS ================= --}}
    <div class="mt-4 text-end">
        <button class="btn btn-primary">Update Asset</button>
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

var quickSupplierModal = null;
if (typeof bootstrap !== 'undefined' && document.getElementById('quickSupplierModal')) {
    quickSupplierModal = new bootstrap.Modal(document.getElementById('quickSupplierModal'));
}

var quickBrandModal = null;
if (typeof bootstrap !== 'undefined' && document.getElementById('quickBrandModal')) {
    quickBrandModal = new bootstrap.Modal(document.getElementById('quickBrandModal'));
}

function showQuickSupplierModalFn() {
    if (quickSupplierModal) {
        quickSupplierModal.show();
    } else {
        $('#quickSupplierModal').show();
    }
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

 const OLD_CATEGORY = "{{ old('category_id', $asset->category_id) }}";
    const OLD_SUBCATEGORY = "{{ old('sub_category_id', $asset->sub_category_id) }}";
    const OLD_ASSETTYPE = "{{ old('asset_type_id', $asset->asset_type_id) }}";
    const OLD_BRANCH = "{{ old('branch_id', $asset->branch_id) }}";
    const OLD_LOCATION = "{{ old('location_id', $asset->location_id) }}";

function loadBranches(companyId, selectedId = null, selectedLocationId = null) {
    const branchDropdown = $('#branch_id');
    branchDropdown.html('<option value="">Loading...</option>');

    if (!companyId) {
        branchDropdown.html('<option value="">Select Branch</option>');
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
                loadLocations(String(selectedId), selectedLocationId);
            }
        }
    ).fail(function () {
        branchDropdown.html('<option value="">Failed to load branches</option>');
    });
}

$('#company_id').on('change', function () {
    $('#location_id').empty().append('<option value="">Select Area</option>');
    loadBranches(this.value, null, null);
});


    function loadSubCategories(categoryId, selectedId = null) {
        const subCategoryDropDown = $('#sub_category_id');
        subCategoryDropDown.html('<option>Loading...</option>');

        if (!categoryId) {
            subCategoryDropDown.html('<option value="">Select Category</option>');
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
                loadAssetTypes(selectedId, OLD_ASSETTYPE);
            }
        });
    }

    $('#category_id').on('change', function () {
        loadSubCategories(this.value);
    });

    function loadAssetTypes(subId, selectedId = null) {
    const assetTypeDropDown = $('#asset_type_id');
    assetTypeDropDown.html('<option>Loading...</option>');

    if (!subId) {
        assetTypeDropDown.html('<option value="">Select Asset Type</option>');
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
            });
        }

        $('#sub_category_id').on('change', function () {
            loadAssetTypes(this.value);
        });


        function loadLocations(branchId, selectedId = null) {
        const locationSelect = $('#location_id');
        locationSelect.html('<option>Loading...</option>');

        if (!branchId) {
            locationSelect.html('<option value="">Select Location</option>');
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

    toggleAssetStructure();
    toggleDepreciationFields();

    if ($('#company_id').val()) {
        loadBranches($('#company_id').val(), OLD_BRANCH, OLD_LOCATION);
    }

    syncSupplierHidden();
    syncBrandHidden();

    if (OLD_CATEGORY) {
        loadSubCategories(OLD_CATEGORY, OLD_SUBCATEGORY);
    }

    $('#assetEditForm').on('submit', function (e) {
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
            alert('Select a branch for the selected company.');
            $('#branch_id').trigger('focus');
            return false;
        }
        return true;
    });

});

</script>

@endsection
