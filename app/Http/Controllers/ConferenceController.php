<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function index(Request $request)
    {
        $workspace_id = isset($request->workspace_id) ? $request->workspace_id : 0;
        $data = Conference::where('workspace_id',$workspace_id)->get();
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'workspace_id' => 'required|integer',
        ]);

        Conference::create($request->all());
        return response()->json(['msg' => 'Conference data Added succesfully','data'=>[], 'status' => true],200);
    }

    public function show($id)
    {
        $data = Conference::findOrFail($id);
        return response()->json(['msg' => 'Conference data Added succesfully','data'=>$data, 'status' => true],200);
    }

    public function update(Request $request, $id)
    {
        $conference = Conference::findOrFail($id);

        $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'workspace_id' => 'required|integer',
        ]);

        $conference->update($request->all());

        return response()->json(['msg' => 'Conference data Updated succesfully','data'=>[], 'status' => true],200);
    }

    public function destroy($id)
    {
        $conference = Conference::findOrFail($id);
        $conference->delete();

        return response()->json(['msg' => 'Conference data Deleted succesfully','data'=>[], 'status' => true],200);
    }
}
