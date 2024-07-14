<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::all();
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'mobile_number' => 'nullable|string',
        ]);

        Customer::create($request->all());
        return response()->json(['msg' => 'Customer added succesfully','data'=>[], 'status' => true],200);
    }

    public function show($id)
    {
        $data = Customer::findOrFail($id);
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'first_name' => 'required',
            'mobile_number' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return response()->json(['msg' => 'Customer updated succesfully','data'=>[], 'status' => true],200);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['msg' => 'Customer deleted succesfully','data'=>[], 'status' => true],200);
    }
}
