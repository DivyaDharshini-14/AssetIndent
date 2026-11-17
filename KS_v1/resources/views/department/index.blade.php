@extends('layouts/contentNavbarLayout')

@section('title', 'Departments')

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
  Departments
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Department List</h5>
            <small class="text-muted">{{ $departments->total() }} total Departments</small>
        </div>
         @can('department:create')
        <a href="{{ route('department.create') }}" class="btn btn-primary">
            Add Department
        </a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $department)
                    <tr>
                        <td class="text-nowrap text-muted">
                            {{ ($departments->currentPage() - 1) * $departments->perPage() + $loop->iteration }}
                        </td>
                        <td>{{ $department->department_name }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                    type="checkbox"
                                    onclick="updateDepartmentStatus(this)"
                                    data-id="{{ $department->id }}"
                                    {{ $department->status === 'active' ? 'checked' : '' }}
                                    style="width:40px;height:20px;cursor:pointer;">
                            </div>
                        </td>
                        <td>
                             @can('department:edit')
                            <a href="{{ route('department.edit', $department->id) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            @endcan 

                             @can('department:delete')
                            <form action="{{ route('department.destroy', $department->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  id="delete-form-{{ $department->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $department->id }})">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            No departments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        @if($departments->hasPages())
            <div class="text-muted">Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of {{ $departments->total() }} Departments</div>
            <div>{{ $departments->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('page-script')
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function updateDepartmentStatus(el) {

    let departmentId = el.getAttribute('data-id');
    let status = el.checked ? 'active' : 'inactive';

    $.ajax({
        url: "{{ route('department.status') }}",
        type: "PUT",
        data: {
            _token: "{{ csrf_token() }}",
            id: departmentId,
            status: status
        },
        success: function (response) {
            if (!response.success) {
                el.checked = !el.checked;
            }
        },
        error: function () {
            el.checked = !el.checked;
        }
    });
}
</script>
@endsection
