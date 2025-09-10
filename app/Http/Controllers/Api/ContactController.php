<?php

// app/Http/Controllers/Api/ContactController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // List all contacts
    public function index()
    {
        return response()->json(Contact::all());
    }

    // Store new contact
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'nullable|string|max:20',
            'lead_score' => 'nullable|integer',
            'tags' => 'nullable|array',   // Accept array
            'tags.*' => 'string',
        ]);

        // Ensure tags is always an array, even if missing
        $validated['tags'] = $request->input('tags', []);

        $contact = Contact::create($validated);

        return response()->json($contact, 201);
    }

    // Show single contact
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact);
    }

    // Update contact
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'sometimes|required|email|unique:contacts,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'lead_score' => 'nullable|integer',
            'tags' => 'nullable|string',
        ]);

        $contact->update($validated);

        return response()->json($contact);
    }

    // Delete contact
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
