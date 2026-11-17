@extends('assets.asset-layout')

@section('asset_section')

<div class="card card-action mb-6">
      <div class="card-header align-items-center py-6">
        <h5 class="card-action-title mb-0">Update Assignment</h5>
        <div class="card-action-element">
          <a class="btn btn-sm btn-primary text-white" href="{{ asset_show_section_url($asset, 'assignments') }}">Back to Assigned List</a>
        </div>
      </div>
      <div class="card-body">

        <form action="{{ route('asset.assignment.update',$assignment) }}" method="POST">

        <div class="row g-3">

           <div class="col-4">
            <label class="form-label form-label-required" for="assign_date">Date <span class="text-danger">*</span></label>
            <input type="date" name="assign_date" class="form-control @error('assign_date') is-invalid @enderror" value="{{ old('assign_date',date('Y-m-d',strtotime($assignment->assign_date))) }}">
            @error('assign_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
           </div>

           <div class="col-4">
            <label class="form-label form-label-required" for="assigned_to">Assigned To <span class="text-danger">*</span></label>
            <select id="assigned_to" name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
              <option value="branch" {{ old('assigned_to',$assignment->assigned_to) == 'branch' ? 'selected' : '' }}>Branch</option>
              <option value="employee" {{ old('assigned_to',$assignment->assigned_to) == 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
            @error('assigned_to')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-4">
            <label class="form-label form-label-required" for="to_company_id">Company <span class="text-danger">*</span></label>
            <select id="to_company_id" name="to_company_id" class="form-select @error('to_company_id') is-invalid @enderror" required>
              <option value="">Select</option>
              @foreach($companies as $company)  
              <option value="{{$company->id}}" {{ old('to_company_id',$assignment->to_company_id) == $company->id ? 'selected' : '' }}>{{$company->name}}</option>
              @endforeach
            </select>
            @error('to_company_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

           <div class="col-4">
            <label class="form-label form-label-required" for="to_branch">Branch <span class="text-danger">*</span></label>
            <select id="to_branch" name="to_branch" class="form-select @error('to_branch') is-invalid @enderror" required>
              <option value="">Select</option>
              @foreach($branches as $branch)  
              <option value="{{$branch->id}}" {{ old('to_branch',$assignment->to_branch) == $branch->id ? 'selected' : '' }}>{{$branch->name}}</option>
              @endforeach
              
            </select>
            @error('to_branch')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-4">
            <label class="form-label form-label-required" for="to_location">Area</label>
            <select id="to_location" name="to_location" class="form-select @error('to_location') is-invalid @enderror">
                <option value="">Select</option>
                @foreach($locations as $location)  
                <option value="{{$location->id}}" {{ old('to_location',$assignment->to_location) == $location->id ? 'selected' : '' }}>{{$location->name}}</option>
                @endforeach

            </select>
            @error('to_location')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>

            <div class="col-4">
            <label class="form-label form-label-required" for="assiged_emoloyee">Employee<span class="text-danger">*</span></label>
            <select id="assiged_emoloyee" name="assiged_emoloyee" class="form-select @error('assiged_emoloyee') is-invalid @enderror">
                <option value="">Select</option>
            </select>
            @error('assiged_emoloyee')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>
       
            <div class="col-4">
            <label class="form-label" for="notes">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes',$assignment->notes) }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

        </div>

         <div class="pt-5 float-end">
            @csrf
            @method('PUT')
            <input type="hidden" name="asset_id" id="asset_id" value="{{$asset->id}}">
                        <button type="submit" class="btn btn-primary">
                            Assign
                        </button>
                        <a href="{{asset_show_section_url($asset, 'assignments') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>

        </form>
       
       

      </div>
    </div> 
 

 

@endsection



@section('page-script')
<script>
$(function () {

    const assignedToValue = "{{ old('assigned_to', $assignment->assigned_to) }}";
    const companyValue    = "{{ old('to_company_id', $assignment->to_company_id) }}";
    const branchValue     = "{{ old('to_branch', $assignment->to_branch) }}";
    const locationValue   = "{{ old('to_location', $assignment->to_location) }}";
    const employeeValue   = "{{ old('assiged_emoloyee', $assignment->assiged_emoloyee) }}";

    const $assignedTo  = $('#assigned_to');
    const $company     = $('#to_company_id');
    const $branch      = $('#to_branch');
    const $location    = $('#to_location');
    const $employee    = $('#assiged_emoloyee');
    const $employeeCol = $employee.closest('.col-4');

    /* ============================
       INITIAL STATE
    ============================ */

    toggleEmployee(assignedToValue === 'employee');

    if (companyValue) {
        loadBranches(companyValue, branchValue);
    }

    /* ============================
       EVENTS
    ============================ */

    $company.on('change', function () {
        const companyId = $(this).val();
        resetBranch();
        resetLocation();
        resetEmployee();

        if (companyId) {
            loadBranches(companyId);
        }
    });

    $branch.on('change', function () {

        const branchId = $(this).val();

        resetLocation();
        resetEmployee();

        if (!branchId) return;

        loadLocations(branchId);

        if ($assignedTo.val() === 'employee') {
            loadEmployees(branchId);
        }
    });

    $assignedTo.on('change', function () {

        const isEmployee = $(this).val() === 'employee';
        toggleEmployee(isEmployee);

        if (isEmployee && $branch.val()) {
            loadEmployees($branch.val());
        } else {
            resetEmployee();
        }
    });

    /* ============================
       LOAD BRANCHES (IMPORTANT)
    ============================ */

    function loadBranches(companyId, selectedBranch = null) {

        $branch.html('<option value="">Loading...</option>');

        $.getJSON(
            "{{ route('ajax.getbranch.withoutscope', ':id') }}".replace(':id', companyId)
        )
        .done(res => {

            $branch.html('<option value="">Select Branch</option>');

            if (res.data?.length) {

                res.data.forEach(branch => {
                    $branch.append(
                        `<option value="${branch.id}">
                            ${branch.name}
                        </option>`
                    );
                });

                if (selectedBranch) {
                    $branch.val(selectedBranch);

                    loadLocations(selectedBranch, locationValue);

                    if (assignedToValue === 'employee') {
                        loadEmployees(selectedBranch, employeeValue);
                    }
                }
            }
        });
    }

    /* ============================
       LOAD LOCATIONS
    ============================ */

    function loadLocations(branchId, selectedLocation = null) {

        $location.html('<option value="">Loading...</option>');

        $.getJSON(
            "{{ route('ajax.getlocation.bybranch', ':id') }}".replace(':id', branchId)
        )
        .done(res => {

            $location.html('<option value="">Select Location</option>');

            if (res.data?.length) {

                res.data.forEach(loc => {
                    $location.append(
                        `<option value="${loc.id}">
                            ${loc.location_name}
                        </option>`
                    );
                });

                if (selectedLocation) {
                    $location.val(selectedLocation);
                }
            }
        });
    }

    /* ============================
       LOAD EMPLOYEES
    ============================ */

    function loadEmployees(branchId, selectedEmployee = null) {

        $employee.html('<option value="">Loading...</option>');

        $.getJSON(
            "{{ route('ajax.getusers.bybranch', ':id') }}".replace(':id', branchId),
            { assignees: 1 }
        )
        .done(res => {
            $employee.html('<option value="">Select Employee</option>');
            appendAssigneeOptions($employee, res.data, selectedEmployee);

            if (selectedEmployee) {
                $employee.val(selectedEmployee);
            }
        });
    }

    function appendAssigneeOptions($select, items, selectedId = null) {
        if (!items?.length) {
            return;
        }

        items.forEach(user => {
            $select.append(
                $('<option>', {
                    value: user.id,
                    text: user.name,
                    selected: String(selectedId) === String(user.id),
                })
            );
        });
    }

    /* ============================
       HELPERS
    ============================ */

    function toggleEmployee(show) {
        if (show) {
            $employeeCol.show();
            $employee.prop('required', true);
        } else {
            $employeeCol.hide();
            $employee.prop('required', false).val('');
        }
    }

    function resetBranch() {
        $branch.html('<option value="">Select Branch</option>');
    }

    function resetLocation() {
        $location.html('<option value="">Select Location</option>');
    }

    function resetEmployee() {
        $employee.html('<option value="">Select Employee</option>');
    }

});
</script>


@endsection
