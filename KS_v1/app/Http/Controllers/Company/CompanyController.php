<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests\Companies\CompanyRequest;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->paginate(50);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(CompanyRequest $request)
    {
        $validated = $request->validated();

        $company=Company::create($validated);

        // $user=auth()->user();

       //  $user->companies()->syncWithoutDetaching([$company->id]);

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $validated = $request->validated();

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
         if ($company->hasDependencies()) {
            return back()->with('error', 'cannot delete company because it has mapped with other records');
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
