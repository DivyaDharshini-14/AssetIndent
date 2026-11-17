@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Status Source')

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
    Edit Status Source
</h4>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Edit Status Source</h5>
                <a href="{{ route('status-sources.index') }}" class="btn btn-secondary">
                    Back to list
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('status-sources.update', $statusSource->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Lead Source <span class="text-danger">*</span></label>
                        <select class="form-select @error('lead_source_id') is-invalid @enderror"
                                name="lead_source_id" required>
                            <option value="">Select Lead Source</option>
                            @foreach($leadSources as $source)
                                <option value="{{ $source->id }}"
                                    {{ $statusSource->lead_source_id == $source->id ? 'selected' : '' }}>
                                    {{ $source->campaign }}
                                </option>
                            @endforeach
                        </select>
                        @error('lead_source_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lead Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('lead_status_id') is-invalid @enderror"
                                name="lead_status_id" required>
                            <option value="">Select Lead Status</option>
                            @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ $statusSource->lead_status_id == $status->id ? 'selected' : '' }}>
                                    {{ ucfirst($status->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('lead_status_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn btn-primary">
                            Update Status Source
                        </button>
                        <a href="{{ route('status-sources.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
