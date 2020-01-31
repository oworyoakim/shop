<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Comment;
use App\Repositories\ISystemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use stdClass;
use DNS1D;

class SalesController extends Controller
{

    /**
     * @var ISystemRepository
     */
    private $repository;

    /**
     * SalesController constructor.
     * @param ISystemRepository $repository
     */
    public function __construct(ISystemRepository $repository)
    {
        $this->repository = $repository;
    }


}
