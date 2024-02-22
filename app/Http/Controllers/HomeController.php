<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => "I'm what I'm and I'm not ashamed",
        ], Response::HTTP_OK);
    }
}
