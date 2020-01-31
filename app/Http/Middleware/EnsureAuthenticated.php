<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Http\Response;

class EnsureAuthenticated
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($user = Sentinel::check())
        {
            if ($role = $user->getActiveRole())
            {
                $request->attributes->set('role', $role);
            }
            if ($branch = $user->getActiveBranch())
            {
                $request->attributes->set('branch', $branch);
            }
            return $next($request);
        }

        $request->session()->put('url.intended', $request->getRequestUri());

        if ($request->ajax())
        {
            return response()->json("Unauthorized Access!", Response::HTTP_UNAUTHORIZED);
        }
        session()->flash('error', "Unauthorized Access!");
        return redirect()->route('login');
    }

}
