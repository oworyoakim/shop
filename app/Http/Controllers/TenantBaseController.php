<?php

namespace App\Http\Controllers;

use App\Traits\Tenant\UsesLoggedInUser;

class TenantBaseController extends Controller
{
    use UsesLoggedInUser;
}
