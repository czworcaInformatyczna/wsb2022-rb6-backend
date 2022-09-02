<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cookie;
use App\Models\Language;
use App\Models\Theme;
use Illuminate\Http\Request;
class CookiesController extends Controller
{
    public function patchLanguages(request $request){
        if(patchCookie($request->value, $request->user()['id'], 'language_id', Language::class)){
            return response([
                'language_id' => $request->value
            ], 200);
        }
        return response([
            'message' => 'Invalid language_id'
        ], 400);
    }
    public function patchThemes(request $request){
        if(patchCookie($request->value, $request->user()['id'], 'theme_id', Theme::class)){
            return response([
                'theme_id' => $request->value
            ], 200);
        }
        return response([
            'message' => 'Invalid theme_id'
        ], 400);
    }
}
