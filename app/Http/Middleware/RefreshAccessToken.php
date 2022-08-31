<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;

class RefreshAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::where('email', $request->user()['email'])->firstOrFail();
        PersonalAccessToken::where('tokenable_id', $user->id)->update(['created_at' => Carbon::now()]);
        return $next($request);
    }
}
