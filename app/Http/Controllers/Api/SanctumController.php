<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SanctumController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()]);
        }
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
        ]);
        
        create_cookie($user->id);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'name' => $user->name,
            'email' => $user->email, 
            'access_token' => $token,
            'token_type' => 'Bearer',
            'theme_id' => $user->cookie->theme->id,
            'language_id' => $user->cookie->language->id,
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
            'message' => 'Invalid login details'
                       ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        auth()->user()->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        create_cookie($user->id);
        return response()->json([
            'name' => $user->name,
            'email' => $user->email, 
            'access_token' => $token,
            'token_type' => 'Bearer',
            'theme_id' => $user->cookie->theme->id,
            'language_id' => $user->cookie->language->id,
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}
