@extends('layouts/contentNavbarLayout')

@section('title', 'Transmissions')

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
    <h4 class="py-3 breadcrumb-wrapper mb-4">
      <span class="text-muted fw-light">Transmissions</span>
    </h4>

    {{-- Stats Cards --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-check-circle text-success bx-lg mb-2"></i>
                    <h3 class="text-success mb-1">{{ $activeTransmissions }}</h3>
                    <small class="text-muted fw-semibold">Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-pause-circle text-warning bx-lg mb-2"></i>
                    <h3 class="text-warning mb-1">{{ $inactiveTransmissions }}</h3>
                    <small class="text-muted fw-semibold">Inactive</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-list-ul bx-lg mb-2"></i>
                    <h3 class="mb-1">{{ $transmissions->total() }}</h3>
                    <small class="text-muted fw-semibold">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <a href="{{ route('transmissions.create') }}" class="btn btn-primary w-100">
                        <i class="bx bx-plus me-1"></i>Add New
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Transmission Types</h5>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-striped table-hover" id="transmissionsTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transmissions as $transmission)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial bg-info rounded-circle">
                                            {{ strtoupper(substr($transmission->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $transmission->name }}</div>
                                        <small class="text-muted">ID: {{ $transmission->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($transmission->status === 'active')
                                    <span class="badge bg-label-success fs-12px">Active</span>
                                @else
                                    <span class="badge bg-label-warning fs-12px">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('transmissions.show', $transmission) }}">
                                            <i class="bx bx-show me-2"></i>View</a></li>
                                        <li><a class="dropdown-item" href="{{ route('transmissions.edit', $transmission) }}">
                                            <i class="bx bx-edit me-2"></i>Edit</a></li>
                                        <li>
                                            <form method="POST" action="{{ route('transmissions.destroy', $transmission) }}" class="d-inline delete-form">
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
                                <i class="bx bx-cog bx-lg text-muted mb-3"></i>
                                <p class="text-muted mb-2">No transmissions found</p>
                                <a href="{{ route('transmissions.create') }}" class="btn btn-primary btn-sm">
                                    Add First Transmission
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $transmissions->links() }}
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
    $('#transmissionsTable').DataTable({
        pageLength: 15,
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        language: { lengthMenu: "_MENU_ per page" }
    });
});
</script>
@endsection
