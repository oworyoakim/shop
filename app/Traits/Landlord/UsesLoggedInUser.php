<?php


namespace App\Traits\Landlord;


use App\Models\Landlord\Admin;
use Illuminate\Support\Facades\Auth;

trait UsesLoggedInUser
{
    /**
     * @return Admin|null
     */
    protected function getUser()
    {
        return Auth::guard('landlord')->user();
    }

    protected function isLoggedIn() {
        return Auth::guard('landlord')->check();
    }
}
