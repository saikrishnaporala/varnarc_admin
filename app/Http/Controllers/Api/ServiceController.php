<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(Service::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'visibility' => 'required|string',
            'publish_at' => 'nullable|date',
            'category' => 'nullable|string',
            'tags' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string',
            'stocks' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'orders' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // ✅ Handle single image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // ✅ Handle gallery
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('services/gallery', 'public');
            }
            $data['gallery'] = json_encode($galleryPaths);
        }

        $service = Service::create($data);
        return response()->json($service, 201);
    }

    public function show(Service $service)
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title'             => 'sometimes|required|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'sometimes|required|string',
            'visibility'        => 'sometimes|required|string',
            'publish_at'        => 'nullable|date',
            'category'          => 'nullable|string',
            'tags'              => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'manufacturer'      => 'nullable|string',
            'stocks'            => 'nullable|integer',
            'price'             => 'nullable|numeric',
            'discount'          => 'nullable|numeric',
            'orders'            => 'nullable|integer',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'gallery.*'         => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'meta_title'        => 'nullable|string|max:255',
            'meta_keywords'     => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
        ]);

        // ✅ Handle single image (replace if new one uploaded)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // ✅ Handle gallery (new uploads)
        $newGallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $newGallery[] = $file->store('services/gallery', 'public');
            }
        }

        // ✅ Handle existing gallery (from frontend JSON)
        $existingGallery = [];
        if ($request->filled('existing_gallery')) {
            $existingGallery = json_decode($request->input('existing_gallery'), true);
        }

        // ✅ Merge both (replace gallery with final list)
        $data['gallery'] = json_encode(array_merge($existingGallery, $newGallery));

        $service->update($data);

        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(null, 204);
    }
}
