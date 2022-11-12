<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Item;
use App\ShopHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DNS1D;

class ItemsController extends TenantBaseController
{
    public function index()
    {
        try
        {
            $loggedInUser = $this->getUser();
            $products = Item::query()->paginate(10);

            $items = $products->getCollection();
            $modifiedItems = $items->map(function ($item) use ($loggedInUser){
                $item->canBeEdited = $loggedInUser->hasAnyAccess(['tenant.items.update']);
                $item->canBeDeleted = $loggedInUser->hasAnyAccess(['tenant.items.delete']);
                $item->canPrintBarcode = $loggedInUser->hasAnyAccess(['tenant.items.print_barcode']);
                return $item;
            });
            $products->setCollection($modifiedItems);

            return response()->json($products);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("GET_ITEMS", $ex);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();

            if (!$loggedInUser->hasAnyAccess(['tenant.items.create']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'unit_id' => 'required|exists:units,id',
                'category_id' => 'required|exists:categories,id',
                'account' => 'required',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "message" => "Validation Errors",
                    "errors" => $validator->errors()->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $settings = ShopHelper::getTenantSettings($loggedInUser->tenant_id);

            // check for margin override
            $account = $request->get('account');
            $margin = 0.00;
            if ($account == 'sales' || $account == 'both')
            {
                $margin = $request->get('margin');
                if (!$margin)
                {
                    if ($settings->enable_margin)
                    {
                        $margin = floatval($settings->profit_margin);
                    } else
                    {
                        $margin = 5.00; // default is 5%
                    }
                }
            }

            $barcode = $request->get('barcode');
            if (!$barcode)
            {
                // generate unique barcode
                $timestamp = Carbon::now()->getTimestamp();
                $barcode = "{$loggedInUser->id}{$timestamp}";
            } elseif (Item::query()->where('barcode', $barcode)->orWhere('secondary_barcode', $barcode)->exists())
            {
                return response()->json([
                    "message" => "The barcode {$barcode} already exists. Enter a new barcode or leave it blank to be assigned one automatically!",
                    "errors" => $validator->errors()->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }
            $title = $request->get('title');
            $description = $request->get('description');
            $unit_id = $request->get('unit_id');
            $category_id = $request->get('category_id');

            Item::create([
                'barcode' => $barcode,
                'title' => $title,
                'account' => $account,
                'margin' => $margin,
                'description' => $description,
                'category_id' => $category_id,
                'unit_id' => $unit_id,
                'user_id' => $loggedInUser->id,
                'tenant_id' => $loggedInUser->tenant_id,
            ]);
            return response()->json('Item Created!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("CREATE_ITEM", $ex);
        }
    }

    public function update(Request $request, Item $item)
    {
        try
        {
            $loggedInUser = $this->getUser();

            if (!$loggedInUser->hasAnyAccess(['tenant.items.update']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'unit_id' => 'required|exists:units,id',
                'category_id' => 'required|exists:categories,id',
                'account' => 'required',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "message" => "Validation Errors",
                    "errors" => $validator->errors()->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $unit_id = $request->get('unit_id');
            $category_id = $request->get('category_id');

            $settings = ShopHelper::getTenantSettings($loggedInUser->tenant_id);

            $title = $request->get('title');
            $description = $request->get('description');
            $account = $request->get('account');

            $margin = 0.00;
            if ($account == 'sales' || $account == 'both')
            {
                $margin = $request->get('margin');
                if (!$margin)
                {
                    if ($settings->enable_margin)
                    {
                        $margin = floatval($settings->profit_margin);
                    } else
                    {
                        $margin = 5.00; // default is 5%
                    }
                }
            }

            $item->update([
                'title' => $title,
                'account' => $account,
                'margin' => $margin,
                'description' => $description,
                'category_id' => $category_id,
                'unit_id' => $unit_id,
            ]);
            return response()->json('Item Updated!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("UPDATE_ITEM", $ex);
        }
    }

    public function delete(Request $request, Item $item)
    {
        try
        {
            $user = $this->getUser();

            if (!$user->hasAnyAccess(['tenant.items.delete']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }
            $item->delete();
            return response()->json('Item Deleted!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("DELETE_ITEM", $ex);
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
        } catch (\Throwable $ex)
        {
            Log::error("PRINT_ITEM_BARCODE: {$ex->getMessage()}");
            return redirect()->route('home');
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
        } catch (\Throwable $ex)
        {
            Log::error("GET_STOCKS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
