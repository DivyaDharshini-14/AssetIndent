@extends('layouts/contentNavbarLayout')

@section('title', 'Asset Depreciation')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Asset Depreciation Report</h4>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-header border-bottom mb-4">
        <h5 class="mb-0">Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('asset.depreciation') }}" class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Search by Asset Code / Asset Name">
            </div>

            <div class="col-md-3">
                <label class="form-label">Branch</label>
                <select class="form-select" name="branch_id">
                    <option value="">All Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="sub_category_id">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('sub_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Asset Type</label>
                <select class="form-select" name="asset_type_id">
                    <option value="">All Types</option>
                    @foreach($assetTypes as $type)
                        <option value="{{ $type->id }}" {{ request('asset_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->asset_type_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Method</label>
                <select class="form-select" name="depreciation_method">
                    <option value="">All Methods</option>
                    <option value="straight_line" {{ request('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight Line</option>
                    <option value="reducing_balance" {{ request('depreciation_method') == 'reducing_balance' ? 'selected' : '' }}>Reducing Balance</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Purchase From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Purchase To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    <i class="bx bx-search"></i>
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div>
            <h5 class="mb-0">Depreciation List</h5>
            <small class="text-muted">{{ $assets->total() }} total assets</small>
        </div>
        
    </div>

    <div class="table-responsive">
        <table class="table card-table" id="depreciationTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Asset</th>
                    <th>Code</th>
                    <th>Category</th>
                    <th>Assigned To</th>
                    <th>Method</th>
                    <th>Purchase Date</th>
                    <th>Purchase Cost</th>
                    <th>Current Value</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>{{ $assets->firstItem() + $loop->index }}</td>
                    <td>
                        <strong>{{ $asset->asset_name }}</strong>
                        <div class="text-muted fs-12px">
                            {{ $asset->assetType?->asset_type_name }}
                        </div>
                    </td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->subCategory?->category_name ?? '--' }}</td>
                    <td>{{ $asset->latestAssignment?->employee?->name ?? '--' }}</td>
                    <td>
                        <span class="badge bg-label-info">
                            {{ ucfirst(str_replace('_',' ',$asset->depreciation_method)) }}
                        </span>
                    </td>
                    <td>{{$asset->purchase_date->format('d M Y')}}</td>
                    <td>₹ {{ number_format($asset->purchase_cost,2) }}</td>                   
                    <td>
                        <strong class="text-success">
                            ₹ {{ number_format($asset->current_value,2) }}
                        </strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        No depreciation data available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!--pagination-->
        @if($assets->hasPages())
        <div class="d-flex justify-content-end px-3 py-2">
            {{$assets->links()}}
        </div>
        @endif
    </div>
</div>

@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
$(function() {
    const table = $('#depreciationTable').DataTable({
        paging: false,
        lengthChange: false,
        info: false,
        responsive: true,
        dom: 'Bfrtip',
        columnDefs: [
            { targets: 0, orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        buttons: ['excel', 'pdf']
    });

    $('#exportBtn').click(function() {
        table.buttons().trigger();
    });
});
</script>
@endsection
