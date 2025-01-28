<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Google\Client;
use Google_Client;

class AuthController extends Controller
{
    // function for register
    public function register(Request $request)
    {
        // validation
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

    function loginGoogle(Request $request){
        $request->validate([
           'id_token' => 'required|string'
        ]);

        // get id token
        $id_token = $request->id_token;
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($id_token);

        // check if payload is valid
        if ($payload) {
            $user = User::where('email', $payload['email'])->first();
            $token = $user->createToken('auth_token')->plainTextToken;
            if ($user) {

                return response()->json([
                    'status' => 'success',
                    'message' => 'User logged in successfully',
                    'data' => [
                        'user' => $user,
                        'token' => $token
                    ]
                ]);
            } else {
                $user = User::create([
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'password' => Hash::make($payload['sub'])
                ]);
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'data' => [
                        'user' => $user,
                        'token' => $token
                    ]
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid id token'
            ], 400);
        }
    }

    // function for login
    public function login(Request $request)
    {
        // validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // check user
        $user = User::where('email', $request->email)->first();

        // check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
}
