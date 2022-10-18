<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cookie;
use App\Models\Language;
use App\Models\Theme;
use Illuminate\Http\Request;
class CookiesController extends Controller
{
    public function patchLanguages(Request $request){

        if($this->setCookie($request->value, $request->user()['id'], 'language_id', Language::class)){
            return response([
                'language_id' => $request->value
            ], 200);
        }
        return response([
            'message' => 'Invalid language_id'
        ], 400);
    }
    
    public function patchThemes(Request $request){
        if($this->setCookie($request->value, $request->user()['id'], 'theme_id', Theme::class)){
            return response([
                'theme_id' => $request->value
            ], 200);
        }
        return response([
            'message' => 'Invalid theme_id'
        ], 400);
    }

    public function setCookie($value, $userId, $tableName, $object){
        if(($object::where('id', $value)->first())){
            Cookie::where('user_id', $userId)->update([$tableName => $value]);
            return true;
        }
        return false;
    }

    public function createCookie(int $userId){
        $user = Cookie::where('user_id', $userId)->first();        
            if($user == NULL){
                Cookie::create([
                    'user_id' => $userId,
                    'theme_id' => 1,
                    'language_id' => 1
                ]);
            }  
    }

    public function nullTest(Request $request){
        return Cookie::orderBy('id', 'desc')->first('id')->id;
    }
}
