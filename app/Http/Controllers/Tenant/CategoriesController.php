<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Category;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class CategoriesController extends TenantBaseController
{
    public function index()
    {
        try
        {
            $categories = Category::all()->transform(function (Category $item) {
                $category = new stdClass();
                $category->id = $item->id;
                $category->slug = $item->slug;
                $category->title = $item->title;
                $category->description = $item->description;
                $category->itemsCount = $item->items()->count();
                $category->canBeEdited = Sentinel::hasAnyAccess(['categories.update']);
                $category->canBeDeleted = Sentinel::hasAnyAccess(['categories.delete']);
                return $category;
            });
            return response()->json($categories);
        } catch (Exception $ex)
        {
            Log::error("GET_CATEGORIES: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['categories.create']))
            {
                throw new Exception("Permission Denied!");
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:categories',
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
            $title = $request->get('title');
            $description = $request->get('description');
            $user = Sentinel::getUser();
            Category::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'user_id' => $user->getUserId()
            ]);
            return response()->json('Category Created!');
        } catch (Exception $ex)
        {
            Log::error("CREATE_CATEGORY: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['categories.update']))
            {
                throw new Exception("Permission Denied!");
            }

            $unitId = $request->get('id');
            $category = Category::query()->find($unitId);

            if (!$category)
            {
                throw new Exception("Category not found!");
            }

            $title = $request->get('title');
            $description = $request->get('description');
            if ($title != $category->title)
            {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|unique:categories',
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
                $category->slug = Str::slug($title);
            }
            $category->title = $title;
            $category->description = $description;
            $category->save();
            return response()->json('Category Updated!');
        } catch (Exception $ex)
        {
            Log::error("UPDATE_CATEGORY: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['categories.delete']))
            {
                throw new Exception("Permission Denied!");
            }

            $categoryId = $request->get('category_id');
            $category = Category::query()->find($categoryId);

            if (!$category)
            {
                throw new Exception("Category not found!");
            }
            settings()->beginTransaction();
            $category->items()->delete();
            $category->delete();
            settings()->commitTransaction();
            return response()->json('Category Deleted!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("DELETE_CATEGORY: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
