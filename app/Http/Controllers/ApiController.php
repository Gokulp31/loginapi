<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => 'validation error'], 400);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $response['token'] = $user->createToken('ApiController');
        $response['user'] = $user->name;
        return response()->json($response, 200);
    }


    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = Auth::user();
            $response['token'] = $user->createToken('ApiController');
            $response['user'] = $user->name;
            return response()->json($response, 200);
        } else {
            return response()->json(['message' => 'Invalid credentials error'], 401);
        }
        // return "hello world";

    }
    public function detail()
    {
        $user = Auth::user();
        $data = [
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
        ];
        $response['user'] = $data;
        return response()->json($response, 200);
    }
}
