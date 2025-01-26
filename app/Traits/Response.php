<?php

namespace App\Traits;

trait Response
{
    public function api_response($status, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    // Success 
    public function success($message)
    {
        return $this->api_response(true, $message, null, 200);
    }

    // Unauthorize 
    public function unauthorize($message)
    {
        return $this->api_response(false, $message, null, 401);
    }

    public function notFound($message)
    {
        return $this->api_response(false, $message, null, 404);
    }

    public function serverError($message)
    {
        return $this->api_response(false, $message, null, 500);
    }

    public function data($data, $message = "success")
    {
        return $this->api_response(true, $message, $data, 200);
    }

    public function validationError($validator)
    {
        return $this->api_response(false, $validator->errors()->first(), null, 422);
    }

    public function badRequest($message)
    {
        return $this->api_response(false, $message, null, 400);
    }
}
