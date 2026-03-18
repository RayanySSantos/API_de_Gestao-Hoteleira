<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;

class RateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Rate::query()->orderBy('id')->get(),
        ]);
    }
}
