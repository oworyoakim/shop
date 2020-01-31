<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Http\Response;

class EnsureManager
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $msg = 'Unauthorized Access to Manager Section of this App!';
        //managers
        if (Sentinel::inRole('manager'))
        {
            return $next($request);
        } elseif ($request->ajax())
        {
            return response('Unauthorized Access!', Response::HTTP_UNAUTHORIZED);
        } else
        {
            session()->flash('warning', $msg);
            Sentinel::logout(null, true);
            return redirect()->route('login')->with('error', $msg);
        }
    }

}
