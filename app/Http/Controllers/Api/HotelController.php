<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;


class HotelController extends Controller
{
    /**
     * Lista todos os hotéis com contagem de relacionamentos.
     *
     * Retorna a quantidade de quartos, tarifas e reservas associadas a cada hotel.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Hotel::query()->withCount(['rooms', 'rates', 'reservations'])->orderBy('id')->get(), // O get Executa a consulta e retorna os dados
        ]);
    }
}
