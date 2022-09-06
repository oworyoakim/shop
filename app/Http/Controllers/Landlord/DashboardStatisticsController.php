<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\LandlordBaseController;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardStatisticsController extends LandlordBaseController
{
    public function index(Request $request)
    {
        try {
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');
            if(empty($startDate)){
                $startDate = Carbon::today();
            }else {
                $startDate = Carbon::parse($startDate);
            }
            if(empty($endDate)){
                $endDate = Carbon::today();
            }else {
                $endDate = Carbon::parse($endDate);
            }

            if($startDate->greaterThan($endDate)){
                $clone = $startDate->clone();
                $startDate = $endDate->clone();
                $endDate = $clone;
            }
            $data = Tenant::active()
                          ->withCurrency()
                          ->withBranchesCount()
                          ->selectRaw('tenants.*')
                          ->get();

            return response()->json($data);
        } catch (\Exception $ex) {
            return $this->handleJsonRequestException("GET_LANDLORD_DASHBOARD_STATISTICS", $ex);
        }
    }
}
