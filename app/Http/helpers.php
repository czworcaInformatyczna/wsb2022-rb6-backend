<?php

use App\Models\Cookie;

    if(!function_exists('create_cookie')) {
        function create_cookie($userId) {
            $user = Cookie::where('user_id', $userId)->first();        
            if($user == NULL){
                Cookie::create([
                    'user_id' => $userId,
                    'theme_id' => 1,
                    'language_id' => 1
                ]);
            }         
        } 
    }
    if(!function_exists('patchCookie')){
        function patchCookie($value, $userID, $tableName, $object){
            if(($object::where('id', $value)->first())){
                Cookie::where('user_id', $userID)->update([$tableName => $value]);
                return true;
            }
            return false;
        }
    }
?>