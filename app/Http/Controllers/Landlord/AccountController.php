<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\LandlordBaseController;
use App\Models\Landlord\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AccountController extends LandlordBaseController
{
    public function healthCheck()
    {
        return response()->json(['version' => app()->version()]);
    }

    public function index()
    {
        if ($this->isLoggedIn())
        {
            return view('landlord.layout');
        }
        return view('auth.login', ['name' => "Shop Landlord"]);
    }

    public function processLogin(Request $request)
    {
        try
        {
            $credentials = [
                "username" => $request->get('loginName'),
                "password" => $request->get('loginPassword'),
            ];
            //dd($credentials);
            $user = Admin::query()->where(["username" => $credentials['username']])->first();
            // dd($user);
            if (!$user)
            {
                return response()->json('Wrong username or password', Response::HTTP_BAD_REQUEST);
            }

            if ($user->isBlocked())
            {
                return response()->json('Your account has been blocked by admin', Response::HTTP_BAD_REQUEST);
            }

            if (!Hash::check($credentials['password'], $user->password))
            {
                return response()->json('Wrong username or password', Response::HTTP_FORBIDDEN);
            }

            Auth::guard('landlord')->login($user);
            $new_session_id = Session::getId(); //get new session ID after user sign in
            if (!empty($user->login_token))
            {
                Session::getHandler()->destroy($user->login_token); // destroy any previous logins
            }
            $user->update(['login_token' => $new_session_id]);
            //get intended URL from session
            $redirectUrl = $request->session()->pull('url.intended');
            return response()->json([
                'message' => "Login Successful!",
                'url' => $redirectUrl
            ]);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("LANDLORD_LOGIN_ERROR", $ex);
        }
    }

    public function logout(Request $request)
    {
        try
        {
            $user = $this->getUser();
            if(!empty($user->login_token)){
                Session::getHandler()->destroy($user->login_token); // destroy any previous logins
            }
            $user->login_token = null;
            $user->save();
            Auth::guard('landlord')->logout();
            return response()->json('Logged Out!');
        } catch (\Exception $ex)
        {
            return $this->handleJsonRequestException("LOGOUT_ERROR", $ex);
        }
    }

    public function getUserData()
    {
        try {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser) {
                return response()->json('No user data!', Response::HTTP_UNAUTHORIZED);
            }
            $userData = $loggedInUser->getUserData();
            return response()->json($userData);
        } catch (\Exception $ex) {
            return $this->handleJsonRequestException("GET_USER_DATA_ERROR", $ex);
        }
    }
}
