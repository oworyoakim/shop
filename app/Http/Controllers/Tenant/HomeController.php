<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Category;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Unit;
use App\Models\Country;
use App\ShopHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use stdClass;

class HomeController extends TenantBaseController
{

    public function index()
    {
        if ($user = Auth::guard('tenant')->user())
        {
            return view('layout', compact('user'));
        }
        return redirect()->route('login');
    }

    public function pos() {
        return view('pos.layout');
    }

    public function manager() {
        return view('manager.layout');
    }

    public function admin() {
        return view('admin.layout');
    }

    public function getUserData(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            $user = new stdClass();
            $user->id = $loggedInUser->id;
            $user->firstName = $loggedInUser->first_name;
            $user->lastName = $loggedInUser->last_name;
            $user->fullName = $loggedInUser->fullName();
            $user->username = $loggedInUser->username;
            $user->avatar = $loggedInUser->avatar;
            $user->email = $loggedInUser->email;
            $user->permissions = $loggedInUser->permissions;
            $user->role = $loggedInUser->role;
            $user->isAdmin = $loggedInUser->isAdmin();
            $user->isManager = $loggedInUser->isManager();
            $user->isCashier = $loggedInUser->isCashier();
            return response()->json($user);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("GET_USER_DATA", $ex);
        }
    }

    public function getFormOptions(Request $request)
    {
        try
        {
            $data = [
                'units' => Unit::all(['id', 'title']),
                'categories' => Category::all(['id', 'title']),
                'suppliers' => Supplier::all(['id', 'name', 'email', 'phone']),
                'customers' => Customer::all(['id', 'name', 'email', 'phone']),
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
            if (!$barcode)
            {
                throw new Exception("Barcode required!");
            }
            $item = ShopHelper::getItemByBarcode($barcode);
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
            $items = ShopHelper::getSalableItems($branch_id);
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
            $items = ShopHelper::getPurchasableItems($branch_id);
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
            $countries = Country::all(['id', 'code', 'name', 'phonecode']);
            return response()->json($countries);
        } catch (Exception $ex)
        {
            Log::error("GET_COUNTRIES: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
