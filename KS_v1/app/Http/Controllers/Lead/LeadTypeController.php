<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadType;

class LeadTypeController extends Controller
{
    public function index() {
        $data = LeadType::latest()->get();
        return view('lead-types.index', compact('data'));
    }

    public function create() {
        return view('lead-types.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:lead_types,name'
        ]);
        LeadType::create($validated);
        return redirect()->route('lead-types.index')->with('success','Lead Type created');
    }

    public function edit(LeadType $leadType) {
        return view('lead-types.edit', compact('leadType'));
    }

    public function update(Request $request, LeadType $leadType) {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:lead_types,name,'.$leadType->id
        ]);
        $leadType->update($validated);
        return redirect()->route('lead-types.index')->with('success','Lead Type updated');
    }

    public function destroy(LeadType $leadType) {
        $leadType->delete();
        return back()->with('success','Lead Type deleted');
    }
}

