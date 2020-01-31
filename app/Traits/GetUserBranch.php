<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 10/18/2018
 * Time: 8:09 AM
 */

namespace App\Traits;


use App\Models\Branch;
use App\Repositories\ISystemRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

trait GetUserBranch
{
    /**
     * @var ISystemRepository
     */
    protected $repository;
    /**
     * @return Branch|null
     */
    protected function getUserBranch(){
        $loggedInUser = Sentinel::getUser();
        if($loggedInUser){
            return  $loggedInUser->getActiveBranch();
        }
        return null;
    }
}
