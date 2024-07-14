<?php

namespace App\Http\Controllers;

use App\Models\OrganizationRoom;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationRoomController extends Controller
{
    public function index()
    {
        $request->validate([
            'organization_id' => 'required',
        ]);
        $rooms = OrganizationRoom::where('organization_id',$request->organization_id)->get();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        dd($request->all());
        $request->validate([
            'organization_id' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'price_per_hour' => 'required|numeric',
            'facilities' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['organization_id', 'name', 'description', 'price_per_hour', 'facilities']);

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $images[] = $path;
            }
            $data['images'] = json_encode($images);
        }

        $room = OrganizationRoom::create($data);

        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = OrganizationRoom::findOrFail($id);
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $room = OrganizationRoom::findOrFail($id);

        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required',
            'description' => 'nullable',
            'price_per_hour' => 'required|numeric',
            'facilities' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['organization_id', 'name', 'description', 'price_per_hour', 'facilities']);

        if ($request->hasFile('images')) {
            if ($room->images) {
                $existingImages = json_decode($room->images, true);
                foreach ($existingImages as $image) {
                    Storage::delete('public/' . $image);
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $images[] = $path;
            }
            $data['images'] = json_encode($images);
        }

        $room->update($data);

        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = OrganizationRoom::findOrFail($id);

        if ($room->images) {
            $existingImages = json_decode($room->images, true);
            foreach ($existingImages as $image) {
                Storage::delete('public/' . $image);
            }
        }

        $room->delete();

        return response()->json(null, 204);
    }
}

