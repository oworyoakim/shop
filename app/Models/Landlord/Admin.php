<?php

namespace App\Models\Landlord;

use App\Traits\PermissionsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;
    use PermissionsTrait;

    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'username',
        'group',
        'permissions',
        'login_token',
        'last_login',
        'avatar',
        'password_last_changed',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password_last_changed' => 'datetime',
        'permissions' => 'array',
    ];

    const GROUP_ADMINISTRATORS = 'administrators';

    public function isBlocked()
    {
        return !$this->active;
    }

    public function getUserData()
    {
        $user = new \stdClass();
        $user->id = $this->id;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->firstName = $this->first_name;
        $user->lastName = $this->last_name;
        $user->lastLogin = $this->last_login;
        $user->avatar = $this->avatar;
        $user->active = !!$this->active;
        $user->permissions = $this->permissions;
        $user->group = $this->group ?: '';

        return $user;
    }

    public function getUserId(){
        return $this->id;
    }
}
