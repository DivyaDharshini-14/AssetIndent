<?php
namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\StatusSource;
use Illuminate\Http\Request;

class StatusSourceController extends Controller
{
    public function index()
    {
        $data = StatusSource::with(['leadSource', 'leadStatus'])->get();
        return view('status-source.index', compact('data'));
    }

    public function create()
    {
        $leadSources  = LeadSource::all();
        $leadStatuses = LeadStatus::all();
        return view('status-source.create', compact('leadSources', 'leadStatuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
        ]);
        StatusSource::create($validated);
        return redirect()->route('status-sources.index')->with('success', 'Status Source created');
    }

    public function destroy(StatusSource $statusSource)
    {
        $statusSource->delete();
        return back()->with('success', 'Status Source deleted');
    }

    public function edit(StatusSource $statusSource)
    {
        
        $leadSources  = LeadSource::all();
        $leadStatuses = LeadStatus::all();

        return view('status-source.edit', compact(
            'statusSource',
            'leadSources',
            'leadStatuses'
        ));

    }

    public function update(Request $request, StatusSource $statusSource)
    {
        $validated = $request->validate([
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
        ]);

        $statusSource->update($validated);

        return redirect()
            ->route('status-sources.index')
            ->with('success', 'Status Source updated');
    }

}
