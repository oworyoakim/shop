<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Illuminate\Http\Response;

class EnsureAdmin
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
        //admins
        if (Sentinel::inRole('admin'))
        {
            return $next($request);
        } elseif ($request->ajax())
        {
            return response('Unauthorized Access!', Response::HTTP_UNAUTHORIZED);
        } else
        {
            $msg = 'Unauthorized Access to the admin Section of this App!';
            session()->flash('warning', $msg);
            Sentinel::logout(null, true);
            return redirect()->route('login')->with('error', $msg);
        }
    }

}
