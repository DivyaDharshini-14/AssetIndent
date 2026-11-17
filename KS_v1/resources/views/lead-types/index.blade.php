@extends('layouts/contentNavbarLayout')

@section('title', 'Lead Types')

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
  Lead Types
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Lead Types List</h5>
        <a href="{{ route('lead-types.create') }}" class="btn btn-primary">
            Add Lead Type
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Lead Type Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $leadType)
                <tr>
                    <td>{{ $leadType->id }}</td>
                    <td>{{ $leadType->name }}</td>
                    <td>
                        <a href="{{ route('lead-types.edit', $leadType->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $leadType->id }})">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">No Lead Types found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
</script>
@endsection
