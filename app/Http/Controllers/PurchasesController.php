<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use App\Repositories\ISystemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use stdClass;
use DNS1D;

class PurchasesController extends Controller
{
    /**
     * @var ISystemRepository
     */
    protected $repository;

    /**
     * PurchasesController constructor.
     * @param ISystemRepository $repository
     */
    public function __construct(ISystemRepository $repository)
    {
        $this->repository = $repository;
    }


}
