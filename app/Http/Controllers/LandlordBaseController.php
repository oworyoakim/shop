<?php

namespace App\Http\Controllers;

use App\Models\Landlord\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class LandlordBaseController extends Controller
{
    /**
     * @return Admin|Authenticatable|null
     */
    public function getUser()
    {
        return Auth::user();
    }
}
