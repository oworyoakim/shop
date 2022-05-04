<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Item;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;
use DNS1D;

class ItemsController extends TenantBaseController
{
    public function index()
    {
        try
        {
            $user = $this->getUser();
            $products = Item::all()->transform(function (Item $item) {
                $product = new stdClass();
                $product->id = $item->id;
                $product->barcode = $item->barcode;
                $product->title = $item->title;
                $product->description = $item->description;
                $product->margin = $item->margin;
                $product->account = $item->account;
                $product->avatar = $item->avatar;
                $product->categoryId = $item->category_id;
                $product->category = null;
                $category = $item->category;
                if ($category)
                {
                    $product->category = new stdClass();
                    $product->category->id = $category->id;
                    $product->category->title = $category->title;
                    $product->category->description = $category->description;
                }
                $product->unitId = $item->unit_id;
                $product->unit = null;
                $unit = $item->unit;
                if ($unit)
                {
                    $product->unit = new stdClass();
                    $product->unit->id = $unit->id;
                    $product->unit->title = $unit->title;
                    $product->unit->description = $unit->description;
                }
                $product->canBeEdited = Sentinel::hasAnyAccess(['items.update']);
                $product->canBeDeleted = Sentinel::hasAnyAccess(['items.delete']);
                $product->canPrintBarcode = Sentinel::hasAnyAccess(['items.print_barcode']);
                return $product;
            });
            return response()->json($products);
        } catch (Exception $ex)
        {
            Log::error("GET_ITEMS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $user = $this->getUser();

            if (!$user->hasAnyAccess(['items.create']))
            {
                throw new Exception("Permission Denied!");
            }
            $user = Sentinel::getUser();
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:items',
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
            // check for margin override
            $account = $request->get('account');
            $margin = 0.00;
            if ($account == 'sales' || $account == 'both')
            {
                $margin = $request->get('margin');
                if (!$margin)
                {
                    if (settings()->get('enable_global_margin'))
                    {
                        $margin = floatval(settings()->get('profit_margin'));
                    } else
                    {
                        $margin = 15.00; // default is 15%
                    }
                }
            }

            $barcode = $request->get('barcode');
            if (!$barcode)
            {
                // generate unique barcode
                $barcode = Item::generateBarcode($user);
            } elseif (Item::exists($barcode))
            {
                throw new Exception("The barcode {$barcode} already exists. Enter a new barcode or leave it blank to be assigned one automatically!");
            }
            $title = $request->get('title');
            $description = $request->get('description');
            $unitId = $request->get('unitId');
            $categoryId = $request->get('categoryId');

            settings()->beginTransaction();

            Item::create([
                'barcode' => $barcode,
                'title' => $title,
                'slug' => Str::slug($title),
                'account' => $account,
                'margin' => $margin,
                'description' => $description,
                'category_id' => $categoryId,
                'unit_id' => $unitId,
                'user_id' => $user->getUserId(),
            ]);
            settings()->commitTransaction();
            return response()->json('Item Created!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("CREATE_ITEM: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $user = $this->getUser();

            if (!$user->hasAnyAccess(['items.update']))
            {
                throw new Exception("Permission Denied!");
            }

            $unitId = $request->get('unitId');
            $categoryId = $request->get('categoryId');
            $itemId = $request->get('id');
            $item = Item::query()->find($itemId);

            if (!$item)
            {
                throw new Exception("Item not found!");
            }

            $title = $request->get('title');
            $description = $request->get('description');
            $account = $request->get('account');

            $margin = 0.00;
            if ($account == 'sales' || $account == 'both')
            {
                $margin = $request->get('margin');
                if (!$margin)
                {
                    if (settings()->get('enable_global_margin'))
                    {
                        $margin = floatval(settings()->get('profit_margin'));
                    } else
                    {
                        $margin = 15.00; // default is 15%
                    }
                }
            }

            if ($title != $item->title)
            {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|unique:items',
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
                $item->title = $title;
                $item->slug = Str::slug($title);
            }
            $item->description = $description;
            $item->account = $account;
            $item->save();
            return response()->json('Item Updated!');
        } catch (Exception $ex)
        {
            Log::error("UPDATE_ITEM: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $user = $this->getUser();

            if (!$user->hasAnyAccess(['items.delete']))
            {
                throw new Exception("Permission Denied!");
            }

            $itemId = $request->get('item_id');
            $item = Item::query()->find($itemId);

            if (!$item)
            {
                throw new Exception("Item not found!");
            }
            settings()->beginTransaction();
            $item->delete();
            settings()->commitTransaction();
            return response()->json('Item Deleted!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("DELETE_ITEM: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function printBarcode(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['items.print_barcode']))
            {
                throw new Exception('Permission Denied!');
            }
            $barcode = $request->get('barcode');
            $count = $request->get('count');
            $item = Item::where('barcode', $barcode)->first();
            if (!$item)
            {
                throw new Exception('Item not found!');
            }
            $barcodeImg = DNS1D::getBarcodePNG($barcode, "C93");
            if (settings()->get('print_barcode_with_logo'))
            {
                $companyLogo = settings()->get('company_logo');
            } else
            {
                $companyLogo = null;
            }
            $companyName = settings()->get('company_name');
            $ranges = [];
            for ($i = 1; $i <= $count; $i++)
            {
                $ranges[] = $i;
            }
            $chunks = array_chunk($ranges, 4);
            return view('barcode', compact('barcode', 'barcodeImg', 'chunks', 'companyLogo', 'companyName'));
        } catch (Exception $ex)
        {
            Log::error("PRINT_ITEM_BARCODE: {$ex->getMessage()}");
            return redirect()->route('home');
        }
    }

    public function getSalableItems(Request $request)
    {
        try
        {
            $branchId = $request->get('branch_id');
            $branch = Branch::query()->find($branchId);
            if ($branch)
            {
                $items = $this->repository->getSalableItems($branch->id);
            } else
            {
                $items = $this->repository->getSalableItems();
            }
            return response()->json($items);
        } catch (Exception $ex)
        {
            Log::error("GET_SALABLE_ITEMS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getPurchasableItems(Request $request)
    {
        try
        {
            $branchId = $request->get('branch_id');
            $branch = Branch::query()->find($branchId);
            if ($branch)
            {
                $items = $this->repository->getPurchasableItems($branch->id);
            } else
            {
                $items = $this->repository->getPurchasableItems();
            }
            return response()->json($items);
        } catch (Exception $ex)
        {
            Log::error("GET_SALABLE_ITEMS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getStocks(Request $request)
    {
        try
        {
            $branchId = $request->get('branch_id');
            $branch = Branch::query()->find($branchId);
            if ($branch)
            {
                $stocks = $this->repository->getStocks($branch->id);
            } else
            {
                $stocks = $this->repository->getStocks();
            }
            return response()->json($stocks);
        } catch (Exception $ex)
        {
            Log::error("GET_STOCKS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
