<?php


namespace App\Traits\Tenant;


use App\Models\Tenant\User;
use Illuminate\Support\Facades\Auth;

trait UsesLoggedInUser
{
    protected function isLoggedIn()
    {
        return Auth::guard('tenant')->check();
    }

    /**
     * @return User|null
     */
    protected function getUser()
    {
        return Auth::guard('tenant')->user();
    }
}
