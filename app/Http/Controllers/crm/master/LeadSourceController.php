<?php

namespace App\Http\Controllers\crm\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\crm\master\LeadSource;

class LeadSourceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Validation for Source Name
            'details' => 'nullable|string', // Validation for Details
        ]);

        $leadSource = LeadSource::create($validated);

        return response()->json([
            'message' => 'Lead Source created successfully',
            'lead_source' => $leadSource,
        ]);
    }

    /**
     * Fetch all Lead Sources.
     */
    public function index()
    {
        $leadSources = LeadSource::all();

        return response()->json($leadSources);
    }

    /**
     * Update a Lead Source.
     */
    public function update(Request $request, $id)
    {
        $leadSource = LeadSource::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255', // Validation for Source Name
            'details' => 'nullable|string', // Validation for Details
        ]);

        $leadSource->update($validated);

        return response()->json([
            'message' => 'Lead Source updated successfully',
            'lead_source' => $leadSource,
        ]);
    }

    /**
     * Delete a Lead Source.
     */
    public function destroy($id)
    {
        $leadSource = LeadSource::findOrFail($id);

        $leadSource->delete();

        return response()->json([
            'message' => 'Lead Source deleted successfully',
        ]);
    }
}
