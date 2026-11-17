<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\Department\DepartmentRequest;

class DepartmentController extends Controller
{

    public function index()
    {
        $departments = Department::orderByDesc('id')->paginate(50);

        return view('department.index', compact('departments'));
    }

    public function create()
    {
        return view('department.create');
    }

    public function store(DepartmentRequest $request)
    {
        $validated = $request->validated();

        $department=Department::create($validated);

        // $user=auth()->user();

        // $user->departments()->syncWithoutDetaching([$department->id]);

        return redirect()
            ->route('department.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('department.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        $validated = $request->validated();

        $department->update($validated);

        return redirect()
            ->route('department.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()
            ->route('department.index')
            ->with('success', 'Department deleted successfully.');
    }

    
    public function updateStatus(Request $request)
    {
        $department = Department::findOrFail($request->id);

        $department->status = $request->status;
        $department->save();

        return response()->json(['success' => true]);
    }
    
}
