<?php

namespace App\Http\Middleware\Tenant;

use App\Traits\Tenant\UsesLoggedInUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureCashier
{
    use UsesLoggedInUser;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $this->getUser();
        //managers
        if ($user->isCashier())
        {
            return $next($request);
        }

        $msg = 'Unauthorized Access to Cashier Section of this App!';

        if ($request->expectsJson())
        {
            return response($msg, Response::HTTP_UNAUTHORIZED);
        }

        session()->flash('warning', $msg);
        Auth::guard('tenant')->logout();
        return redirect('/login')->with('error', $msg);
    }

}
