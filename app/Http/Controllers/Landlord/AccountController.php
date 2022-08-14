<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\LandlordBaseController;
use Illuminate\Http\Request;

class AccountController extends LandlordBaseController
{
    public function index()
    {
        if (!$this->isLoggedIn())
        {
            return view('landlord.login');
        }
        return view('landlord.layout');
    }

    public function processLogin(Request $request)
    {
        try
        {
            return response()->json("Okay");
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("LANDLORD_LOGIN_ERROR", $ex);
        }
    }
}
