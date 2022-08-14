<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountController extends TenantBaseController
{
    public function login()
    {
        if (!Auth::guard('tenant')->check())
        {
            return view('tenant.login');
        }
        $user = Auth::guard('tenant')->user();
        if ($user->isAdmin() || $user->isSupervisor() || $user->isAccountant())
        {
            return redirect('admin/dashboard');
        }
        if ($user->isManager())
        {
            return redirect('manager/dashboard');
        }
        return redirect('pos');
    }

    public function processLogin(Request $request)
    {
        try{
            $credentials = [
                "username" => $request->get('loginName'),
                "password" => $request->get('loginPassword'),
            ];
            //dd($credentials);
            $user = User::query()->where(["username" => $credentials['username']])->first();
            // dd($user);
            if (!$user)
            {
                return response()->json('Wrong username or password', Response::HTTP_BAD_REQUEST);
            }

            if ($user->isBlocked())
            {
                return response()->json('Your account has been blocked by admin', Response::HTTP_BAD_REQUEST);
            }

            if ($user->login_token && $user->isCashier())
            {
                return response()->json('You are already logged in. Contact your manager for help!', Response::HTTP_BAD_REQUEST);
            }

            $loggedIn = Auth::guard('tenant')->attempt($credentials);
            if(!$loggedIn) {
                return response()->json('Invalid Credentials!', Response::HTTP_FORBIDDEN);
            }
            $new_session_id  = Session::getId(); //get new session ID after user sign in
            if(!empty($user->login_token)){
                Session::getHandler()->destroy($user->login_token); // destroy any previous logins
            }
            $user->update(['login_token' => $new_session_id]);
            //get intended URL from session
            $redirectUrl = $request->session()->pull('url.intended');
            return response()->json([
                'message' => "Login Successful!",
                'url' => $redirectUrl
            ]);
        }catch (\Throwable $ex){
            return $this->handleJsonRequestException("TENANT_LOGIN_ERROR", $ex);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::guard('tenant')->user();
            $user->update(['login_token' => null]);
            Auth::guard('tenant')->logout();
            return response()->json('Logged Out!');
        }catch (\Throwable $ex) {
            return $this->handleJsonRequestException("TENANT_LOGOUT_ERROR", $ex);
        }
    }
}
