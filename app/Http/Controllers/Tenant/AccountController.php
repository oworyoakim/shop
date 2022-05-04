<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends TenantBaseController
{
    public function login()
    {
        if (Auth::guard('tenant')->check())
        {
            return redirect()->route('home');
        }
        return view('login');
    }

    public function processLogin(Request $request)
    {
        try{
            $credentials = [
                "username" => $request->get('loginName'),
                "password" => $request->get('loginPassword'),
            ];
            if (Auth::guard('tenant')->attempt($credentials))
            {
                //logged in
                return response()->json("Login Successful!");
            }
            //invalid credentials
            return response()->json('Invalid Credentials!',Response::HTTP_FORBIDDEN);
        }catch (Exception $ex){
            Log::error("LOGIN: {$ex->getMessage()}");
            return response()->json($ex->getMessage(),Response::HTTP_FORBIDDEN);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('tenant')->user();
        Auth::guard('tenant')->logout();
        session()->flash('Logged Out!');
        // for ajax requests
        if ($request->ajax())
        {
            return response()->json('Logged Out!');
        }
        return redirect()->route('login');
    }
}
