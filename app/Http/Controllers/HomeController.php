<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Repositories\ISystemRepository;
use App\Models\Country;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use stdClass;

class HomeController extends Controller
{
    /**
     * @var ISystemRepository
     */
    private $repository;

    public function __construct(ISystemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        if (!Sentinel::check())
        {
            return redirect()->route('login');
        }
        return view('layout');
    }

    public function getUserData(Request $request)
    {
        try
        {
            $loggedInUser = Sentinel::getUser();
            $user = new stdClass();
            $user->id = $loggedInUser->id;
            $user->firstName = $loggedInUser->first_name;
            $user->lastName = $loggedInUser->last_name;
            $user->fullName = $loggedInUser->fullName();
            $user->username = $loggedInUser->username;
            $user->avatar = $loggedInUser->avatar;
            $user->email = $loggedInUser->email;
            $user->permissions = $loggedInUser->permissions;
            $user->role = null;
            if ($role = $loggedInUser->roles()->first())
            {
                $user->role = new stdClass();
                $user->role->id = $role->id;
                $user->role->slug = $role->slug;
                $user->role->name = $role->name;
                $user->role->permissions = $role->permissions;
            }
            $user->isAdmin = Sentinel::inRole('admin');
            $user->isManager = Sentinel::inRole('manager');
            $user->isCashier = Sentinel::inRole('cashier');
            return response()->json($user);
        } catch (Exception $ex)
        {
            Log::error("GET_USER_DATA: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getFormSelectionOptions(Request $request)
    {
        try
        {
            $data = [
                'units' => Unit::all(['id','title']),
                'categories' => Category::all(['id','title']),
                'roles' => Role::all(['id','name']),
                'suppliers' => Supplier::all(['id','name','email','phone']),
                'customers' => Customer::all(['id','name','email','phone']),
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            Log::error("GET_FORM_SELECTION_OPTIONS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getItemByBarcode(Request $request)
    {
        try
        {
            $barcode = $request->get('barcode');
            if(!$barcode){
                throw new Exception("Barcode required!");
            }
            $item = $this->repository->getItemByBarcode($barcode);
            return response()->json($item);
        } catch (Exception $ex)
        {
            Log::error("GET_ITEM_BY_BARCODE: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getSalableProducts(Request $request)
    {
        try
        {
            $branch_id = $request->get('branch_id');
            $items = $this->repository->getSalableItems($branch_id);
            return response()->json($items);
        } catch (Exception $ex)
        {
            Log::error("GET_SALABLE_PRODUCTS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getPurchasableProducts(Request $request)
    {
        try
        {
            $branch_id = $request->get('branch_id');
            $items = $this->repository->getPurchasableItems($branch_id);
            return response()->json($items);
        } catch (Exception $ex)
        {
            Log::error("GET_PURCHASABLE_PRODUCTS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getCountries(Request $request)
    {
        try
        {   
            $countries = Country::all(['id','code','name', 'phonecode']);
            return response()->json($countries);
        } catch (Exception $ex)
        {
            Log::error("GET_COUNTRIES: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
