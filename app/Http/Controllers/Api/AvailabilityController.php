<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __invoke(Request $request, Room $room, ReservationService $reservationService): JsonResponse
    {
        /**
         * Valida os dados de entrada.
         * Garante que as datas são válidas e que check_in é anterior ao check_out.
         */
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'before:check_out'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        /**
         * Retorna a disponibilidade do quarto em formato JSON.
         */
        return response()->json([
            'data' => [
                'room_id' => $room->id, // ID do quarto consultado
                // Verifica disponibilidade através do service (regra de negócio)
                'available' => $reservationService->isRoomAvailable($room, $validated['check_in'], $validated['check_out']),
                'check_in' => $validated['check_in'], // Data de entrada informada
                'check_out' => $validated['check_out'], // Data de saída informada
            ],
        ]);
    }
}
