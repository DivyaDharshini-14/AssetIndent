@extends('layouts/contentNavbarLayout')

@section('title', 'Assets')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
@endsection

@section('page-style')
    <style>
        .avatar-initial {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        .fs-12px { font-size: 0.75rem !important; }
        .form-container {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
            border: 1px solid #e3e6f0;
            margin-bottom: 1.5rem;
        }
    </style>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-0">Assets</h4>
      </div>
    </div>

    {{-- Search & Filter Container --}}
    <div class="card mb-4">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Quick Filters</h5>
        </div>
        <div class="card-body pt-2">
            <form method="GET" action="{{ route('asset.index') }}" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Search by Asset Code / Asset Name / Serial No">
                </div>

                <div class="col-md-3">
            <label class="form-label">Company</label>
            <select id="company_id" name="company_id"
                    class="form-select @error('company_id') is-invalid @enderror">
                <option value="">Select Company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                         {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            
        </div>

<div class="col-md-3">
            <label class="form-label">Branch </label>
            <select id="branch_id" name="branch_id" 
                    class="form-select select2 @error('branch_id') is-invalid @enderror">
                <option value="">Select Branch</option>  
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                         {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach               
            </select>
           
        </div>


                 <div class="col-md-3">
                    <label class="form-label">Asset Type</label>
                    <select class="form-select" name="asset_type_id">
                        <option value="">All Types</option>
                        @foreach($assetTypes as $assetType)
                            <option value="{{ $assetType->id }}" {{ request('asset_type_id') == $assetType->id ? 'selected' : '' }}>
                                {{ $assetType->asset_type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="sub_category_id">
                        <option value="">All Categories</option>
                        @foreach($subCategories as $subCategory)
                            <option value="{{ $subCategory->id }}" {{ request('sub_category_id') == $subCategory->id ? 'selected' : '' }}>
                                {{ $subCategory->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                 <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endforeach
                         
                    </select>
                </div>

                 <div class="col-md-3">
                    <label class="form-label">Conditions</label>
                    <select class="form-select" name="condition">
                        <option value="">All Conditions</option>
                       @foreach ($conditions as $condition)
        <option value="{{ $condition->value }}" {{ request('condition') == $condition->value ? 'selected' : '' }}>
            {{ $condition->label() }}
        </option>
    @endforeach
                    </select>
                </div>


                 <div class="col-md-3">
                        <label class="form-label form-label-required">Purchase Date From  </label>
                        <input
                            type="date"
                            name="purchase_date_from"
                            class="form-control"
                            value="{{ old('purchase_date_from', request('purchase_date_from')) }}"
                           
                        >
                        
                    </div>

                     <div class="col-md-3">
                        <label class="form-label form-label-required">Purchase Date To </label>
                        <input
                            type="date"
                            name="purchase_date_to"
                            class="form-control"
                            value="{{ old('purchase_date_to', request('purchase_date_to')) }}"                           
                        >
                         
                    </div>

               
                
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Assets List</h5>
                <small class="text-muted">{{ $assets->total() }} total Assets</small>
            </div>
            <div class="d-flex gap-2">
                @can('asset:create')

                <a href="{{ route('asset.import') }}" class="btn btn-primary btn-sm"> <i class="bx bx-import me-1"></i> Import Asset</a>


                <a href="{{ route('asset.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Add Asset
                </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table card-table " id="dealerLocationsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Area</th>
                        <th>Category</th>
                        <th>Asset Code</th>
                        <th>Status</th>
                        <th>Operational Status</th>
                        <th>Assigned To</th>
                        <th>Purchase Date</th>
                        <th>Purchase Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>{{ $assets->firstItem() + $loop->index }}.</td>
                        <td>

                <div class="d-flex justify-content-start align-items-center customer-name">
                 @if($asset->icon == 'images')   
                <div class="avatar-wrapper">
                  <div class="avatar me-4 rounded-2 bg-label-secondary">
                    <a href="{{ $asset->doc_url }}" data-lightbox="gallery">
                <img src="{{ $asset->doc_url }}" alt="{{ $asset->asset_name }}" class="rounded">
                    </a>   
                 </div>
                </div>
                 @endif
                <div class="d-flex flex-column">
                  <span class="fw-medium text-nowrap text-heading">{{ $asset->asset_name }}</span>
                  <small>{{ $asset->assetType?->asset_type_name }}</small>
                </div>
              </div>


                            
                        </td>

                        <td>
                            @php
                                $branchName = $asset->branch->name
                                    ?? $asset->latestAssignment?->toBranch?->name
                                    ?? $asset->latestAssignment?->fromBranch?->name
                                    ?? null;
                            @endphp
                            {{ $branchName ?: '--' }}
                        </td>

                        <td>
                            @php
                                $locationName = $asset->location->location_name
                                    ?? $asset->latestAssignment?->toBranchLocation?->location_name
                                    ?? $asset->latestAssignment?->fromBranchLocation?->location_name
                                    ?? null;
                            @endphp
                            {{ $locationName ?: '--' }}
                        </td>

                        <td>{{ $asset->subCategory?->category_name ?? '-' }}</td>

                        <td>{{ $asset->asset_code }}</td>

                        <td>
                            @php $displayAssetStatus = $asset->effective_status; @endphp
                            @if($displayAssetStatus)
                            <span class="badge bg-label-{{ $displayAssetStatus->color() }}">
                                {{ $displayAssetStatus->label() }}
                            </span>
                            @endif
                        </td>

                        <td>{!! $asset->operational_status_label ?? '--' !!}</td>

                        <td>
                            @php
                                $assignment = $asset->latestAssignment;
                                $assignedLabel = '--';
                                if ($assignment && $asset->status !== \App\AssetStatus::AVAILABLE) {
                                    $assignedLabel = $assignment->employee?->name ?: '--';
                                }
                            @endphp
                            {{ $assignedLabel }}
                        </td>

                        <td>
                            {{ optional($asset->purchase_date)->format('d M Y') }}
                        </td>

                        <td>
                            ₹ {{ number_format($asset->purchase_cost, 2) }}
                        </td>

                        <td>
                            <div class="dropdown">
                                <button class="btn p-0 btn-sm" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('asset.show', $asset->id) }}">
                                            <i class="bx bx-show me-1"></i> View
                                        </a>
                                    </li>
                                    @if ($asset->isEditable())
                                        @can('asset:edit')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('asset.edit', $asset->id) }}">
                                                    <i class="bx bx-edit me-1"></i> Edit
                                                </a>
                                            </li>
                                        @endcan
                                        @can('asset:delete')
                                            <li>
                                                <form method="POST"
                                                    action="{{ route('asset.destroy', $asset->id) }}"
                                                    class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5 text-muted">
                            No assets found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            @if($assets->hasPages())
                <div class="text-muted">Showing {{ $assets->firstItem() ?? 0 }} to {{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }} assets</div>
                <div>{{ $assets->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>

 $('.select2').select2({
    placeholder: "Select",
    allowClear: true,
    closeOnSelect: true, 
    width: '100%'
});

 function loadBranches(companyId, selectedId = null) {
            const branchDropdown = $('#branch_id');
            branchDropdown.html('<option>Loading...</option>');

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
                            let selected = selectedId == value.id ? 'selected' : '';
                            branchDropdown.append(
                                `<option value="${value.id}" ${selected}>${value.name}</option>`
                            );
                        });
                    } else {
                        branchDropdown.append('<option value="">No branches found</option>');
                    }
                }
            ).fail(function () {
                branchDropdown.html('<option value="">Failed to load branches</option>');
            });
        }

        $('#company_id').on('change', function () {
            loadBranches(this.value);
        });

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

$(function() {
    const table = $('#dealerLocationsTable').DataTable({
        paging: false,
        lengthChange: false,
        info: false,
        responsive: true,
        dom: 'Bfrtip',
        language: { search: "Search assets:" },
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        buttons: ['excel', 'pdf']
    });

    
});
</script>
@endsection
