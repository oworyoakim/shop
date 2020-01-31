<?php

namespace App\Http\Middleware;

use App\Repositories\IEmployeeRepository;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Closure;

class EnsureCashier {

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        //cashiers
        if (Sentinel::inRole('cashier')) {
            return $next($request);
        }elseif ($request->ajax()) {
            return response('Unauthorized Access!', Response::HTTP_UNAUTHORIZED);
        }else{
            $msg = 'Unauthorized Access to Cashier Section of this App!';
            session()->flash('warning',$msg);
            Sentinel::logout(null, true);
            return redirect()->route('login')->with('error', $msg);
        }
    }

}
