<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Theme;

class ThemesController extends Controller
{
    public function get(){
        return Theme::all();
    }
}
