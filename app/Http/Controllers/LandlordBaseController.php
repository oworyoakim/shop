<?php

namespace App\Http\Controllers;

use App\Traits\Landlord\UsesLoggedInUser;

class LandlordBaseController extends Controller
{
    use UsesLoggedInUser;
}
