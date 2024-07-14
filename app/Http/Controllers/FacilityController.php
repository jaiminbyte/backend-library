<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $workspace_id = isset($request->workspace_id) ? $request->workspace_id : 0;
        // dd($workspace_id,$request);
        $data = Facility::where('workspace_id',$workspace_id)->get();
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'workspace_id' => 'required|integer',
        ]);

        Facility::create($request->all());
        return response()->json(['msg' => 'Facility Added succesfully','data'=>[], 'status' => true],200);
    }

    public function show($id)
    {
        $data = Facility::findOrFail($id);
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function update(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'workspace_id' => 'required|integer',
        ]);

        $facility->update($request->all());

        return response()->json(['msg' => 'Facility Updated succesfully','data'=>[], 'status' => true],200);
    }

    public function destroy($id)
    {
        $facility = Facility::findOrFail($id);
        $facility->delete();

        return response()->json(['msg' => 'Facility Deleted succesfully','data'=>[], 'status' => true],200);
    }
}
