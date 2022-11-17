<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;

class LanguagesController extends Controller
{
    public function get(){
        return Language::all();
    }
}
