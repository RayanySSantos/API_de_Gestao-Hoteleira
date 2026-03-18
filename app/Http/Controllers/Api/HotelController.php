<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Hotel::query()->withCount(['rooms', 'rates', 'reservations'])->orderBy('id')->get(),
        ]);
    }
}
