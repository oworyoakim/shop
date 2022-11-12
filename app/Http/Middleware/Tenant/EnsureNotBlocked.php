<?php

namespace App\Http\Middleware\Tenant;

use App\Traits\Tenant\UsesLoggedInUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class EnsureNotBlocked {
    use UsesLoggedInUser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next) {
        $user = $this->getUser();

        if($user->isBlocked()) {
            $msg = 'Your account has been blocked by admin!';
            if ($request->expectsJson()) {
                return response($msg, Response::HTTP_UNAUTHORIZED);
            }
            session()->flash('error',$msg);
            Auth::guard('tenant')->logout();
            $request->session()->put('url.intended', $request->url());
            return redirect("/login");
        }

        //dd($user);
        $request->attributes->add([
            'role' => $user->group,
            'group' => $user->group
        ]);
        View::share("user", $user);
        return $next($request);
    }

}
