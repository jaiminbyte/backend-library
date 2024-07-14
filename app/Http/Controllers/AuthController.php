<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Workspace;
use App\Models\Facility;
use App\Models\Conference;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function users(Request $request){
        $validator = Validator::make($request->all(), [
            'role' => 'required'
        ]);
        $data = User::where('role',$request->role)->get();
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            // 'user_name' => 'required|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(isset($request->id) && $request->id > 0){
            $uid = $request->id;
            User::update([
                'name' => $request->name,
                'phone' => $request->phone,
                // 'user_name' => $request->user_name,
                'email' => $request->email,
                'role' => isset($request->role) ? $request->role : 'user'
            ]);
        } else {
            $uid = User::insertGetId([
                'name' => $request->name,
                'phone' => $request->phone,
                // 'user_name' => $request->user_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => isset($request->role) ? $request->role : 'user'
            ]);
        }
        $user = User::where('id',$uid)->first();

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token', 'uid'), 201);
    }

    public function user_register(Request $request)
    {

        if(isset($request->id) && $request->id > 0){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required|numeric',
                // 'user_name' => 'required|unique:users',
                'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
                'role' => 'required'
            ]);
            
            $uid = $request->id;
            User::where('id',$request->id)->update([
                'name' => $request->name,
                'phone' => $request->phone,
                // 'user_name' => $request->user_name,
                'email' => $request->email,
                'role' => isset($request->role) ? $request->role : 'user'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required|numeric',
                // 'user_name' => 'required|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'role' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $uid = User::insertGetId([
                'name' => $request->name,
                'phone' => $request->phone,
                // 'user_name' => $request->user_name,
                'email' => $request->email,
                'role' => isset($request->role) ? $request->role : 'user'
            ]);
        }
        $user = User::where('id',$uid)->first();

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token', 'uid'), 201);
    }

    public function google_register(Request $request)
    {
        // dd($request->all());
        $user = User::where('email',$request->email)->where('google_login','yes')->first();
        if(isset($user)){
            $uid = isset($user->id) ? $user->id : 0;
        } else {
            $validator = Validator::make($request->all(), [
                'displayName' => 'required',
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $uid = User::inserGetId([
                'name' => $request->displayName,
                'phone' => isset($request->phoneNumber) ? $request->phoneNumber : NULL,
                'email' => $request->email,
                'google_register' => 'yes',
                'password' => Hash::make('test@123'),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token', 'uid'), 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        $uid = isset($user->id) ? $user->id : 0;
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(compact( 'token', 'uid'), 201);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh(JWTAuth::getToken()));
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(JWTAuth::user());
    }

}

