<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionTestController extends Controller
{
    public function permission1(){
        return response()->json([
            'message' => "You have access to pemission1 page"
        ]); 
    }

    public function permission2(){
        return response()->json([
            'message' => "You have access to pemission2 page"
        ]); 
    }

    public function permission3(){
        return response()->json([
            'message' => "You have access to pemission3 page"
        ]); 
    }
}
