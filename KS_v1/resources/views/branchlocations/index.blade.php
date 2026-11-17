@extends('layouts/contentNavbarLayout')

@section('title', 'Area')

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
  Area
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Area List</h5>
            <small class="text-muted">{{ $locations->total() }} total Areas</small>
        </div>
        @can('location:create')
            <a href="{{ route('location.create') }}" class="btn btn-primary">
            Add Area
        </a>
        @endcan
        
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-nowrap">#</th>
                    <th>Area</th>
                    <th>Branch</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td class="text-nowrap text-muted">
                            {{ ($locations->currentPage() - 1) * $locations->perPage() + $loop->iteration }}
                        </td>
                        <td>{{ $location->location_name }}</td>
                        <td>
                            @php $branches = $location->_branches ?? collect(); @endphp
                             @if($branches->isEmpty())
                                    --
                            @else
                                 {{ $branches->take(3)->pluck('name')->implode(', ') }}@if($branches->count() > 3)...@endif
                            @endif
                        </td>
                        <td>
                        {{ $location->branch->company->name ?? '--' }}
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                    type="checkbox"
                                    onclick="updateDesignationStatus(this)"
                                    data-id="{{ $location->id }}"
                                    {{ $location->status === 'active' ? 'checked' : '' }}
                                    style="width:40px;height:20px;cursor:pointer;">
                            </div>
                        </td>
                        <td>
                             @can('location:edit')
                            <a href="{{ route('location.edit', $location->id) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            @endcan
                            
 @can('location:delete')
                            <form action="{{ route('location.destroy', $location->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  id="delete-form-{{ $location->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $location->id }})">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            No Area found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        @if($locations->hasPages())
            <div class="text-muted">Showing {{ $locations->firstItem() ?? 0 }} to {{ $locations->lastItem() ?? 0 }} of {{ $locations->total() }} Areas</div>
            <div>{{ $locations->links() }}</div>
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

function updateDesignationStatus(el) {

    let designationId = el.getAttribute('data-id');
    let status = el.checked ? 'active' : 'inactive';

    $.ajax({
        url: "{{ route('location.status') }}",
        type: "PUT",
        data: {
            _token: "{{ csrf_token() }}",
            id: designationId,
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
