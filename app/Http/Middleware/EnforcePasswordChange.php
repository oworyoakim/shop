<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnforcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $loggedInUser = Sentinel::getUser();
        //dd($loggedInUser);
        $passwordDuration = intval(settings()->get('user_password_days'));
        if (!$passwordDuration) {
            $passwordDuration = 30; // default to 30 days
        }
        // if password not set or its more than 30 days since the password was last changed
        if (is_null($loggedInUser->password_last_changed) || $loggedInUser->password_last_changed->lessThan(Carbon::now()->subDays($passwordDuration))) {
            return redirect()->route('force-password-change')->with('error', 'You must change your password to continue!');
        }
        return $next($request);
    }
}
