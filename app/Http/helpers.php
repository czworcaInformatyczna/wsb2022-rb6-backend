<?php

use Carbon\Carbon;
use App\Models\PersonalAccessToken;
use App\Models\User;

if(!function_exists('sanctumRefreshTest')){
    function sanctumRefreshTest($request){
        $user = User::where('email', $request->user()['email'])->firstOrFail();
        PersonalAccessToken::where('tokenable_id', $user->id)->update(['created_at' => Carbon::now()]);
    }
}

?>