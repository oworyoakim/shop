<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function login()
    {
        if (Sentinel::check())
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
            if ($user = Sentinel::authenticate($credentials))
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

    public function forcePasswordChange()
    {
        return view('force-password-change');
    }

    public function processForcePasswordChange(Request $request)
    {
        return response()->json('Ok');
    }

    public function logout(Request $request)
    {
        $user = Sentinel::getUser();
        Sentinel::logout($user, true);
        // for ajax requests
        if ($request->ajax())
        {
            session()->flash('Logged Out!');
            return response()->json('Logged Out!');
        }
        session()->flash('Logged Out!');
        return redirect()->route('login');
    }
}
