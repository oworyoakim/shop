<?php

namespace App\Http\Middleware\Tenant;

use App\Traits\Tenant\UsesLoggedInUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
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
        //admins
        if ($user->isAdmin() || $user->isAccountant() || $user->isSupervisor())
        {
            return $next($request);
        }

        if ($request->expectsJson())
        {
            return response('Unauthorized Access!', Response::HTTP_UNAUTHORIZED);
        }

        $msg = 'Unauthorized Access to the admin Section of this App!';
        session()->flash('warning', $msg);
        Auth::guard('tenant')->logout();
        return redirect('/login')->with('error', $msg);
    }

}
