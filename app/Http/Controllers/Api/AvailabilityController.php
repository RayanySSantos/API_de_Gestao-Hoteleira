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
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'before:check_out'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        return response()->json([
            'data' => [
                'room_id' => $room->id,
                'available' => $reservationService->isRoomAvailable($room, $validated['check_in'], $validated['check_out']),
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
            ],
        ]);
    }
}
