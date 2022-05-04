<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\LandlordBaseController;
use Illuminate\Http\Request;

class AccountController extends LandlordBaseController
{
    public function index(){
        return view('welcome');
    }
}
