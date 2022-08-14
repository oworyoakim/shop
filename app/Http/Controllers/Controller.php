<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param string $key
     * @param \Exception $ex
     *
     * @return JsonResponse
     */
    protected function handleJsonRequestException(string $key, \Exception $ex) : JsonResponse {
        Log::error($key, [
            "Message" => $ex->getMessage(),
            "File" => $ex->getFile(),
            "Line" => $ex->getLine(),
        ]);
        $msg = trans('general.server_error');
        return response()->json($msg, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
