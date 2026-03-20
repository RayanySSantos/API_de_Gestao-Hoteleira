<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;

class RateController extends Controller
{
    /**
     * Lista todas as tarifas cadastradas no sistema.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            // Ordena as tarifas pelo ID
            'data' => Rate::query()->orderBy('id')->get(), // Executa a consulta e retorna os registros

        ]);
    }
}
