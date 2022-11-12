<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends TenantBaseController
{
    public function index()
    {
        try
        {
            $loggedInUser = $this->getUser();
            $productCategories = Category::query()->withCount('items')->paginate(10);

            $categories = $productCategories->getCollection();
            $modifiedCategories = $categories->map(function ($category) use ($loggedInUser){
                $category->canBeEdited = $loggedInUser->hasAnyAccess(['tenant.categories.update']);
                $category->canBeDeleted = $loggedInUser->hasAnyAccess(['tenant.categories.delete']);
                return $category;
            });

            $productCategories->setCollection($modifiedCategories);

            return response()->json($productCategories);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('GET_CATEGORIES', $ex);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['tenant.categories.create']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "message" => "Validation Errors",
                    "errors" => $validator->errors()->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $title = $request->get('title');
            $description = $request->get('description');

            Category::create([
                'title' => $title,
                'description' => $description,
                'user_id' => $loggedInUser->id,
                'tenant_id' => $loggedInUser->tenant_id,
            ]);
            return response()->json('Category Created!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('CREATE_CATEGORY', $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['tenant.categories.update']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }

            $category = Category::query()->find($id);

            if (!$category)
            {
                return response()->json([
                    'message' => "Category not found!"
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "message" => "Validation Errors",
                    "errors" => $validator->errors()->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $title = $request->get('title');
            $description = $request->get('description');

            $category->update([
                'title' => $title,
                'description' => $description,
            ]);
            return response()->json('Category Updated!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("UPDATE_CATEGORY", $ex);
        }
    }

    public function delete(Request $request, $id)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['tenant.categories.delete']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }

            $category = Category::query()->find($id);

            if (!$category)
            {
                return response()->json([
                    'message' => "Category not found!"
                ], Response::HTTP_FORBIDDEN);
            }
            DB::transaction(function () use ($category) {
                $category->items()->delete();
                $category->delete();
            });
            return response()->json('Category Deleted!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("DELETE_CATEGORY", $ex);
        }
    }
}
