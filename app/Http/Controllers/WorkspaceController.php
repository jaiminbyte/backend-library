<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index()
    {
        $data = Workspace::all();
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'user_id' => 'required|integer',
            'time_from' => 'reuired',
            'time_to' => 'reuired',
        ]);
        Workspace::create($request->all());
        return response()->json(['msg' => 'Workspace Added succesfully','data'=>[], 'status' => true],200);
    }

    public function show($id)
    {
        $data = Workspace::findOrFail($id);
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function update(Request $request, $id)
    {
        $workspace = Workspace::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'user_id' => 'required|integer',
            'time_from' => 'nullable|date_format:H:i',
            'time_to' => 'nullable|date_format:H:i',
        ]);

        $workspace->update($request->all());

        return response()->json(['msg' => 'Workspace Updated succesfully','data'=>[], 'status' => true],200);
    }

    public function destroy($id)
    {
        $workspace = Workspace::findOrFail($id);
        $workspace->delete();

        return response()->json(['msg' => 'Workspace deleted succesfully','data'=>[], 'status' => true],200);
    }
}
