<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Carbon\Carbon;

use function PHPUnit\Framework\isNull;

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

    public function forgotPassword(Request $request){
        //He forgor 💀
        if(User::where('email', $request->email)->first()){
            $token = Str::random(30);
            PasswordReset::where('email', $request->email)->delete();
            PasswordReset::create([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]);
            Mail::to($request->email)
                ->send(new ForgotPassword($request->email, $token));
        }
        return response([
            'message' => 'Email has been send'
        ], 200);
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed'
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()]);
        }
        $passwordReset = PasswordReset::where('email', $request->email)->first();
        if($passwordReset == NULL){
            return response([
                'message' => 'Bad data'
            ], 400);
        }
        if(!Carbon::parse($passwordReset->created_at)->addDays(1)>Carbon::now()){
            return response([
                'message' => 'Bad data'
            ], 400);
        }
        if(!Hash::check($request->token, $passwordReset->token)){
            return response([
                'message' => 'Bad data'
            ], 400);
        }
        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        PasswordReset::where('email', $request->email)->delete();
        return "działa";
    }
}
