<?php


namespace App\Traits;


use App\Http\Resources\API;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait CustomResponse
{
    /**
     * success response
     * @param $data
     * @param int $code
     * @return JsonResponse|object
     */
    public function successResponse($data, $code = Response::HTTP_OK)
    {
        return (new API([
            'success' => true,
            'data' => $data
        ]))->response()->setStatusCode($code);
    }


    /**
     * success response
     * @param $message
     * @param int $code
     * @return JsonResponse
     */
    public function errorResponse($message, $code)
    {
        return (new API([
            'success' => false,
            'error' => $message,
            'code' => $code,
        ]))->response()->setStatusCode($code);
    }
}
