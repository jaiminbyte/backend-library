<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();
        return response()->json($organizations);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'image' => 'nullable|image',
            'description' => 'nullable',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ]);

        $data = [];
        $data['name'] = $request->name;
        $data['address'] = $request->address;
        $data['description'] = $request->description;
        $data['opening_time'] = $request->opening_time;
        $data['closing_time'] = $request->closing_time;
        $data['dayoff'] = json_encode($request->closing_time);
        // dd($data);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $organization = Organization::create($data);

        return response()->json($organization, 201);
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);
        return response()->json($organization);
    }

    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'image' => 'nullable|image',
            'description' => 'nullable',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($organization->image) {
                Storage::delete('public/' . $organization->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $organization->update($data);

        return response()->json($organization);
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);

        if ($organization->image) {
            Storage::delete('public/' . $organization->image);
        }

        $organization->delete();

        return response()->json(null, 204);
    }
}

