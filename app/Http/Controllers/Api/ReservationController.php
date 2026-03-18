<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RoomUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ReservationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Reservation::query()
                ->with(['hotel', 'room', 'rate', 'guests', 'prices'])
                ->orderBy('check_in')
                ->get(),
        ]);
    }

    public function store(StoreReservationRequest $request, ReservationService $reservationService): JsonResponse
    {
        try {
            $reservation = $reservationService->create($request->validated());
        } catch (RoomUnavailableException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Reservation created successfully.',
            'data' => $reservation,
        ], 201);
    }

    public function show(Reservation $reservation): JsonResponse
    {
        return response()->json([
            'data' => $reservation->load(['hotel', 'room', 'rate', 'guests', 'prices']),
        ]);
    }

    public function update(
        UpdateReservationRequest $request,
        Reservation $reservation,
        ReservationService $reservationService
    ): JsonResponse {
        try {
            $reservation = $reservationService->update($reservation, $request->validated());
        } catch (RoomUnavailableException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Reservation updated successfully.',
            'data' => $reservation,
        ]);
    }

    public function destroy(Reservation $reservation): Response
    {
        $reservation->delete();

        return response()->noContent();
    }
}
