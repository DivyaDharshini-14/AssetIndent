@extends('layouts/contentNavbarLayout')

@section('title', 'Lead Sources')

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
  Lead Sources
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Lead Sources List</h5>
        <a href="{{ route('lead-sources.create') }}" class="btn btn-primary">
            Add Lead Source
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Campaign</th>
                    <th>Enquiry Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->campaign }}</td>
                    <td>
                        @if($item->enquiryType)
                            <span class="badge 
                                @if($item->enquiryType->name === 'Tele-In') bg-primary
                                @elseif($item->enquiryType->name === 'Digital') bg-success
                                @elseif($item->enquiryType->name === 'Walk-In') bg-warning
                                @elseif($item->enquiryType->name === 'Field') bg-danger
                                @else bg-secondary
                                @endif
                            ">
                                {{ $item->enquiryType->name }}
                            </span>
                        @else
                            <span class="badge bg-secondary">N/A</span>
                        @endif
                    </td>
                    <td>
                       
                        <a href="{{ route('lead-sources.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('lead-sources.destroy', $item->id) }}" method="POST" class="d-inline" id="delete-form-{{ $item->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No Lead Sources found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (optional, if you use paginate()) --}}
    <div class="card-footer">
        {{-- If using paginate --}}
        {{-- {{ $data->links() }} --}}
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
