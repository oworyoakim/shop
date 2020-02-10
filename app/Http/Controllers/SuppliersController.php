<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class SuppliersController extends Controller
{
    public function index()
    {
        try
        {
            $suppliers = Supplier::all()->transform(function (Supplier $item) {
                $supplier = new stdClass();
                $supplier->id = $item->id;
                $supplier->name = $item->name;
                $supplier->phone = $item->phone;
                $supplier->email = $item->email;
                $supplier->country = $item->country;
                $supplier->city = $item->city;
                $supplier->address = $item->address;
                $supplier->canBeEdited = Sentinel::hasAnyAccess(['suppliers.update']);
                $supplier->canBeDeleted = Sentinel::hasAnyAccess(['suppliers.delete']);
                return $supplier;
            });
            return response()->json($suppliers);
        } catch (Exception $ex)
        {
            Log::error("GET_SUPPLIERS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
    public function store(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['suppliers.create']))
            {
                throw new Exception("Permission Denied!");
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required|alphanum',
                'country' => 'required|alpha',
                'city' => 'required',
                'address' => 'required',
            ]);
            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                throw new Exception("Validation Error: {$errors}");
            }

            $name = $request->get("name");
            $address = $request->get("address");
            $city = $request->get("city");
            $country = $request->get("country");
            $email = $request->get("email");
            $phone = $request->get("phone");
            Supplier::create([
                "name"=>$name,
                "address"=>$address,
                "city"=>$city,
                "country"=>$country,
                "email"=>$email,
                "phone"=>$phone,
            ]);
            return response()->json("Supplier Created!");
        } catch (Exception $ex)
        {
            Log::error("SAVE_SUPPLIER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
    public function update(Request $request)
    {
        try
        {    
            if (!Sentinel::hasAnyAccess(['suppliers.update']))
            {
                throw new Exception("Permission Denied!");
            }

            $supplierId = $request->get('id');
            $supplier = Supplier::query()->find($supplierId);

            if (!$supplier)
            {
                throw new Exception("Supplier not found!");
            }

            $name = $request->get("name");
            $address = $request->get("address");
            $city = $request->get("city");
            $country = $request->get("country");
            $email = $request->get("email");
            $phone = $request->get("phone");

            $supplier->name = $name;
            $supplier->address = $address;
            $supplier->city = $city;
            $supplier->country = $country;
            $supplier->email = $email;
            $supplier->phone = $phone;
            $supplier->save();
            return response()->json("Supplier Updated!");
        } catch (Exception $ex)
        {
            Log::error("UPDATE_SUPPLIERS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
    public function delete(Request $request)
    {   
        try
        {
            if(!Sentinel::hasAnyAccess(['suppliers.delete']))
            {
                throw new Exception("Permission Denied");
            }

            $supplierId = $request->get('supplier_id');
            $supplier = Supplier::query()->find($supplierId);

            if (!$supplier)
            {
                throw new Exception("Supplier not found!");
            }
            // console.log('>>',$supplier);
            settings()->beginTransaction();
            $supplier->delete();
            settings()->commitTransaction();
            return response()->json("Supplier Deleted");
        } catch (Exception $ex)
        {
            Log::error("DELETE_SUPPLIER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}