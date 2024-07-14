<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;

class ModuleController extends Controller
{
    public function get_modules(){
        $data = Module::get();
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }
}
