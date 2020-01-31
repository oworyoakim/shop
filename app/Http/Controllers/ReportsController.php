<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Repositories\ISystemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use stdClass;

class ReportsController extends Controller
{
    /**
     * @var ISystemRepository
     */
    protected $repository;

    /**
     * ReportsController constructor.
     * @param ISystemRepository $repository
     */
    public function __construct(ISystemRepository $repository)
    {
        $this->repository = $repository;
    }


}
