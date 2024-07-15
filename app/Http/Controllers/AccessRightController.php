<?php

namespace App\Http\Controllers;

use App\Models\AccessRight;
use Illuminate\Http\Request;

class AccessRightController extends Controller
{
    public function index()
    {
        return response()->json(['msg' => '','data'=>AccessRight::all(), 'status' => true],200);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if(!empty($request->all())){
            foreach ($request->all() as $key => &$value) {
                $count = AccessRight::where('user_id',$key)->count();
                if($count > 0){
                    $value['user_id'] = $key;
                    $accessRight = AccessRight::where('user_id',$key)->update($value);
                } else {
                    $value['user_id'] = $key;
                    $accessRight = AccessRight::create($value);
                }
            }
        }
        // $validated = $request->validate([
        //     'dashboard' => 'required|boolean',
        //     'librarian' => 'required|boolean',
        //     'user' => 'required|boolean',
        //     'booking' => 'required|boolean',
        //     'books' => 'required|boolean',
        // ]);

        // $accessRight = AccessRight::create($validated);

        return response()->json(['msg' => '','data'=>[], 'status' => true],200);
    }

    public function show($id)
    {
        $accessRight = AccessRight::findOrFail($id);

        return response()->json($accessRight);
    }

    public function update(Request $request, $id)
    {
        if(!empty($request->all())){
            foreach ($request->all() as $key => &$value) {
                $count = AccessRight::where('user_id',$key)->count();
                if($count > 0){
                    $value['user_id'] = $key;
                    $accessRight = AccessRight::where('user_id',$key)->update($value);
                } else {
                    $value['user_id'] = $key;
                    $accessRight = AccessRight::create($value);
                }
            }
        }
        return response()->json(['msg' => '','data'=>[], 'status' => true],200);
    }

    public function destroy($id)
    {
        $accessRight = AccessRight::findOrFail($id);
        $accessRight->delete();

        return response()->json(null, 204);
    }
}

