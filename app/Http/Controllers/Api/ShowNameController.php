<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class ShowNameController extends Controller
{
    public function showName(request $request){
        return $request->user()['name'];
    }
}
