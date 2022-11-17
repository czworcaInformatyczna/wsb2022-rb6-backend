<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionTestController extends Controller
{
    public function permission1(){
        return "You have access to pemission1 page";
    }

    public function permission2(){
        return "You have access to pemission2 page";
    }

    public function permission3(){
        return "You have access to pemission3 page";
    }
}
