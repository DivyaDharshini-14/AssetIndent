@extends('layouts/contentNavbarLayout')

@section('title', 'Lead Status')

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
  Lead Status
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Lead Status List</h5>
        <a href="{{ route('lead-statuses.create') }}" class="btn btn-primary">
            Add Lead Status
        </a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $status)
                <tr>
                    <td>{{ $status->id }}</td>
                    <td>
                        @if($status->name === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Closed</span>
                        @endif
                    </td>
                    <td>
                       
                        <a href="{{ route('lead-statuses.edit', $status->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('lead-statuses.destroy', $status->id) }}" method="POST" class="d-inline" id="delete-form-{{ $status->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $status->id }})">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

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
