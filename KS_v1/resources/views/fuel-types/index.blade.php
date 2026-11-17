@extends('layouts/contentNavbarLayout')

@section('title', 'Fuel Types')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
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
    </style>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-0">Fuel Types</h4>
      </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-check-circle text-success bx-lg mb-2"></i>
                    <h3 class="text-success mb-1">{{ $activeFuelTypes }}</h3>
                    <small class="text-muted fw-semibold">Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-pause-circle text-warning bx-lg mb-2"></i>
                    <h3 class="text-warning mb-1">{{ $inactiveFuelTypes }}</h3>
                    <small class="text-muted fw-semibold">Inactive</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-droplet bx-lg mb-2 text-primary"></i>
                    <h3 class="mb-1">{{ $fuelTypes->total() }}</h3>
                    <small class="text-muted fw-semibold">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <a href="{{ route('fuel-types.create') }}" class="btn btn-primary w-100">
                        <i class="bx bx-plus me-1"></i>Add Fuel Type
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Fuel Types List</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" id="exportBtn">
                    <i class="bx bx-export me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-striped table-hover" id="fuelTypesTable">
                <thead class="table-light">
                    <tr>
                        <th>Fuel Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fuelTypes as $fuelType)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ strtoupper(substr($fuelType->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $fuelType->name }}</div>
                                        <small class="text-muted">ID: {{ $fuelType->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($fuelType->status === 'active')
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0 btn-sm" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('fuel-types.show', $fuelType) }}">
                                            <i class="bx bx-show me-1"></i>View</a></li>
                                        <li><a class="dropdown-item" href="{{ route('fuel-types.edit', $fuelType) }}">
                                            <i class="bx bx-edit me-1"></i>Edit</a></li>
                                        <li>
                                            <form method="POST" action="{{ route('fuel-types.destroy', $fuelType) }}" class="d-inline delete-form">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="bx bx-gas-pump bx-lg text-muted mb-3"></i>
                                <p class="text-muted mb-2">No fuel types configured</p>
                                <a href="{{ route('fuel-types.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-plus me-1"></i>Add First Fuel Type
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            @if($fuelTypes->hasPages())
                <div class="text-muted">
                    Showing {{ $fuelTypes->firstItem() ?? 0 }} to {{ $fuelTypes->lastItem() ?? 0 }} of {{ $fuelTypes->total() }} types
                </div>
                {{ $fuelTypes->links() }}
            @endif
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
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
    const table = $('#fuelTypesTable').DataTable({
        pageLength: 15,
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        dom: 'Bfrtip',
        language: { lengthMenu: "_MENU_ per page" },
        buttons: ['excel', 'pdf']
    });

    $('#exportBtn').on('click', function() {
        table.buttons().trigger();
    });
});
</script>
@endsection
