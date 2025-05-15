<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function api_response($status,$message,$data = [],$code=200){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ],$code);
    }
}
