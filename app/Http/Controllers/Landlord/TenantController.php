<?php

namespace App\Http\Controllers\Landlord;

use App\ShopHelper;
use App\Http\Controllers\Controller;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        try
        {
            $user = $this->getUser();
            $rules = Tenant::VALIDATION_RULES;

            Log::info("CREATE_TENANT_REQUEST", [
                "User" => $user->username,
                "Payload" => Arr::except($request->all(), ['credentials']),
            ]);

            $validationErrors = $this->validateData($request->all(), $rules);

            if (!empty($validationErrors))
            {
                Log::error("CREATE_TENANT_VALIDATION_ERROR", [
                    "message" => "Data validation failure",
                    "errors" => $validationErrors
                ]);
                return response()->json([
                    "message" => "Data validation failure",
                    "errors" => $validationErrors
                ], Response::HTTP_BAD_REQUEST);
            }
            $loginCredentials = (array) $request->get('credentials');
            // dd($loginCredentials);
            $username = Arr::get($loginCredentials,'loginName');
            $password = Arr::get($loginCredentials,'loginPassword');
            $data = [
                'name' => $request->get('name'),
                'subdomain' => $request->get('subdomain'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'address' => $request->get('address'),
                'country' => $request->get('country'),
                'city' => $request->get('city'),
                'website' => $request->get('website'),
                'user_id' => $user->id,
                'username' => $username,
                'password' => $password,
            ];
            // create the tenant
            $tenant = ShopHelper::createTenant($data);
            return response()->json("Tenant Created!");
        } catch (\Exception $ex)
        {
            return $this->handleJsonRequestException("CREATE_TENANT_ERROR", $ex);
        }
    }

    public function update(Request $request, $id)
    {

    }

    public function block(Request $request, $id)
    {

    }

    public function unblock(Request $request, $id)
    {

    }
}
