<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Http\Response;

class EnsureAjaxRequest
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
        if ($request->ajax())
        {
            return $next($request);
        }
        session()->flash('error', "Unauthorized Request!");
        return redirect()->route('home');
    }

}
