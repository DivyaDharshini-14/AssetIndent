@extends('assets.asset-layout')
 

@section('asset_section')



<div class="card card-action mb-6">
      <div class="card-header align-items-center py-6">
        <h5 class="card-action-title mb-0">Assign Asset</h5>
        <div class="card-action-element">
          <a class="btn btn-sm btn-primary text-white" href="{{asset_show_section_url($asset, 'assignments') }}">Back to Assigned List</a>
        </div>
      </div>
      <div class="card-body">

        <form action="{{ route('asset.assignment.store') }}" method="POST">

        <div class="row g-3">

           <div class="col-4">
            <label class="form-label form-label-required" for="assign_date">Date <span class="text-danger">*</span></label>
            <input type="date" name="assign_date" class="form-control @error('assign_date') is-invalid @enderror" value="{{ old('assign_date',date('Y-m-d')) }}">
            @error('assign_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
           </div>

        <div class="col-4">
            <label class="form-label form-label-required" for="assigned_to">Assigned To <span class="text-danger">*</span></label>
            <select id="assigned_to" name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
              <option value="branch">Branch</option>
              <option value="employee">Employee</option>
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
              <option value="{{$company->id}}" {{ old('to_company_id') == $company->id ? 'selected' : '' }}>{{$company->name}}</option>
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
            </select>
            @error('to_branch')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-4">
            <label class="form-label form-label-required" for="to_location">Area</label>
            <select id="to_location" name="to_location" class="form-select @error('to_location') is-invalid @enderror">
                <option value="">Select</option>
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
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

        </div>

         <div class="pt-5 float-end">
            @csrf
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

    /* ===============================
     * OLD VALUES FROM BACKEND
     * =============================== */
    const oldAssignedTo = "{{ old('assigned_to', 'branch') }}";
    const oldCompany    = "{{ old('to_company_id') }}";
    const oldBranch     = "{{ old('to_branch') }}";
    const oldLocation   = "{{ old('to_location') }}";
    const oldEmployee   = "{{ old('assiged_emoloyee') }}";

    /* ===============================
     * Cache DOM
     * =============================== */
    const $assignedTo  = $('#assigned_to');
    const $company     = $('#to_company_id');
    const $branch      = $('#to_branch');
    const $location    = $('#to_location');
    const $employee    = $('#assiged_emoloyee');
    const $employeeCol = $employee.closest('.col-4');

    /* ===============================
     * Initial Setup
     * =============================== */

    // Set old assigned_to
    $assignedTo.val(oldAssignedTo);
    toggleEmployee(oldAssignedTo === 'employee');

    // If old company exists, reload chain
    if (oldCompany) {
        loadBranches(oldCompany, oldBranch);
    }

    if (oldBranch) {
        loadLocations(oldBranch, oldLocation);

        if (oldAssignedTo === 'employee') {
            loadEmployees(oldBranch, oldEmployee);
        }
    }

    /* ===============================
     * Events
     * =============================== */

    $assignedTo.on('change', function () {
        const isEmployee = $(this).val() === 'employee';
        toggleEmployee(isEmployee);

        if (!isEmployee) {
            resetEmployee();
        } else if ($branch.val()) {
            loadEmployees($branch.val());
        }
    });

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

    /* ===============================
     * UI Helpers
     * =============================== */

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

    /* ===============================
     * Load Branches
     * =============================== */

    function loadBranches(companyId, selectedId = null) {
        $branch.html('<option value="">Loading...</option>');

        $.getJSON(
            "{{ route('ajax.getbranch.withoutscope', ':id') }}".replace(':id', companyId)
        )
        .done(res => {
            resetBranch();

            if (res.data?.length) {
                res.data.forEach(branch => {
                    $branch.append(
                        `<option value="${branch.id}" ${selectedId == branch.id ? 'selected' : ''}>
                            ${branch.name}
                        </option>`
                    );
                });

                if (selectedId) {
                    $branch.val(selectedId).trigger('change');
                }
            }
        })
        .fail(() => {
            resetBranch();
        });
    }

    /* ===============================
     * Load Locations
     * =============================== */

    function loadLocations(branchId, selectedId = null) {
        $location.html('<option value="">Loading...</option>');

        $.getJSON(
            "{{ route('ajax.getlocation.bybranch', ':id') }}".replace(':id', branchId)
        )
        .done(res => {
            resetLocation();

            if (res.data?.length) {
                res.data.forEach(loc => {
                    $location.append(
                        `<option value="${loc.id}" ${selectedId == loc.id ? 'selected' : ''}>
                            ${loc.location_name}
                        </option>`
                    );
                });
            }
        })
        .fail(() => {
            resetLocation();
        });
    }

    /* ===============================
     * Load Employees
     * =============================== */

    function loadEmployees(branchId, selectedId = null) {
        $employee.html('<option value="">Loading...</option>');

        $.getJSON(
            "{{ route('ajax.getusers.bybranch', ':id') }}".replace(':id', branchId),
            { assignees: 1 }
        )
        .done(res => {
            resetEmployee();
            appendAssigneeOptions($employee, res.data, selectedId);
        })
        .fail(() => {
            resetEmployee();
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

});
</script>


@endsection
