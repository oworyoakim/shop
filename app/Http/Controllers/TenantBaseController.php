<?php

namespace App\Http\Controllers;

use App\Models\Tenant\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class TenantBaseController extends Controller
{
    /**
     * @return User|Authenticatable|null
     */
    public function getUser()
    {
        return Auth::guard('tenant')->user();
    }
}
