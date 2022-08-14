<?php

namespace App\Http\Middleware\Landlord;

use App\Traits\Landlord\UsesLoggedInUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class EnsureAuthenticated {
    use UsesLoggedInUser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next) {
        // get the subdomain
        $subdomain = $request->route()->parameter('subdomain');
        //dd($subdomain);

        if (!Auth::guard('landlord')->check())
        {
            $msg = 'Unauthorized Access!';
            Auth::guard('landlord')->logout();
            $request->session()->put('url.intended', $request->url());
            if ($request->expectsJson())
            {
                return response()->json($msg, Response::HTTP_UNAUTHORIZED);
            }
            session()->flash('error', $msg);
            return redirect('/login');
        }

        $user = $this->getUser();

        if ($user->isBlocked())
        {
            $msg = 'Your account has been blocked by admin!';
            if ($request->expectsJson())
            {
                return response($msg, Response::HTTP_UNAUTHORIZED);
            }
            session()->flash('error', $msg);
            Auth::guard('landlord')->logout();
            $request->session()->put('url.intended', $request->url());
            return redirect("/login");
        }

        View::share("user", $user);

        // forget the subdomain from the session
        $request->route()->forgetParameter('subdomain');

        return $next($request);
    }

}
