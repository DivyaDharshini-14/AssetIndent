@extends('layouts/contentNavbarLayout')

@section('title', 'Create Vehicle Model')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/formValidation.min.css') }}">
@endsection

@section('page-style')
    <style>
        .form-modern {
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            max-width: 100%;
            margin: 0;
        }
        .input-modern {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            transition: all 0.2s ease;
            background: #fafbfc;
            height: 56px;
        }
        .input-modern:focus {
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .btn-indigo-modern {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .status-card {
            background: linear-gradient(135deg, #f7f5ff 0%, #f0f2ff 100%);
            border: 1px solid #e0e7ff;
            border-radius: 12px;
            padding: 1.75rem;
            margin: 1rem 0;
        }
        .form-section { padding: 2rem 2.5rem; }
        .form-section:last-child { border-bottom: none; }
    </style>
@endsection

@section('content')
    {{-- Full Width Form --}}
    <div class="container-fluid px-3 px-md-4">
        <div class="row g-0">
            <div class="col-12">
                <div class="form-modern mb-4">
                    {{-- Header --}}
                    <div class="p-4 border-bottom" style="background: linear-gradient(135deg, #f7f5ff 0%, #f0f2ff 100%);">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1, #4f46e5); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx bx-car text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-semibold">Create Vehicle Model</h5>
                                    <small class="text-muted">Complete all required fields marked with *</small>
                                </div>
                            </div>
                            <a href="{{ route('vehicle-models.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Back to List
                            </a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('vehicle-models.store') }}" id="modelForm">
                        @csrf

                        {{-- Model Name --}}
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bx bx-car text-indigo-600"></i>
                                Model Information
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-lg-8">
                                    <label class="form-label fw-medium mb-2">Model Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control input-modern @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           placeholder="XUV500, XUV700, Scorpio">
                                    @error('name')
                                        <div class="invalid-feedback mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Configuration Grid --}}
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bx bx-grid-alt text-indigo-600"></i>
                                Configuration
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-medium mb-2">Segment <span class="text-danger">*</span></label>
                                    <select class="form-select input-modern @error('segment_id') is-invalid @enderror" name="segment_id" required>
                                        <option value="">Select Segment</option>
                                        @foreach($segments as $segment)
                                            <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                                {{ $segment->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-medium mb-2">Fuel Type <span class="text-danger">*</span></label>
                                    <select class="form-select input-modern @error('fuel_type_id') is-invalid @enderror" name="fuel_type_id" required>
                                        <option value="">Select Fuel</option>
                                        @foreach($fuelTypes as $fuelType)
                                            <option value="{{ $fuelType->id }}" {{ old('fuel_type_id') == $fuelType->id ? 'selected' : '' }}>
                                                {{ $fuelType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-medium mb-2">Transmission <span class="text-danger">*</span></label>
                                    <select class="form-select input-modern @error('transmission_id') is-invalid @enderror" name="transmission_id" required>
                                        <option value="">Select Transmission</option>
                                        @foreach($transmissions as $transmission)
                                            <option value="{{ $transmission->id }}" {{ old('transmission_id') == $transmission->id ? 'selected' : '' }}>
                                                {{ $transmission->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-medium mb-2">Company <span class="text-danger">*</span></label>
                                    <select class="form-select input-modern @error('company_id') is-invalid @enderror" name="company_id" required>
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mt-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-medium mb-2">Dealer Location <span class="text-danger">*</span></label>
                                    <select class="form-select input-modern @error('dealer_location_id') is-invalid @enderror" name="dealer_location_id" required>
                                        <option value="">Select Location</option>
                                        @foreach($dealerLocations as $location)
                                            <option value="{{ $location->id }}" {{ old('dealer_location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} ({{ $location->city }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bx bx-toggle-right text-indigo-600"></i>
                                Status
                            </div>
                            <div class="status-card mx-4">
                                <div class="row align-items-center g-3">
                                    <div class="col-lg-8">
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input" type="checkbox" id="statusSwitch" checked>
                                            <label class="form-check-label fw-semibold" for="statusSwitch">
                                                Model is ready for use
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="p-4 border-top bg-gray-50">
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('vehicle-models.index') }}" class="btn btn-outline-secondary px-5 py-2 fs-6">
                                    <i class="bx bx-x me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-indigo-modern px-6 py-2 fs-6">
                                    <i class="bx bx-check me-2"></i>Create Model
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/bootstrap5.min.js') }}"></script>
@endsection

@section('page-script')
<script>
$(function() {
    // Status toggle sync
    $('#statusSwitch').on('change', function() {
        $('#status').val(this.checked ? 'active' : 'inactive');
    });

    // Form validation
    FormValidation.formValidation('#modelForm', {
        fields: {
            name: { validators: { notEmpty: { message: 'Model name required' } } },
            segment_id: { validators: { notEmpty: { message: 'Select segment' } } },
            transmission_id: { validators: { notEmpty: { message: 'Select transmission' } } },
            fuel_type_id: { validators: { notEmpty: { message: 'Select fuel type' } } },
            dealer_location_id: { validators: { notEmpty: { message: 'Select dealer location' } } },
            company_id: { validators: { notEmpty: { message: 'Select company' } } }
        },
        plugins: {
            bootstrap5: new FormValidation.plugins.Bootstrap5(),
            submitButton: new FormValidation.plugins.SubmitButton()
        }
    });
});
</script>
@endsection
