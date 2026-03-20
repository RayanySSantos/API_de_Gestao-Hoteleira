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
    /**
     * Lista todas as reservas com seus relacionamentos.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Reservation::query()
                ->with(['hotel', 'room', 'rate', 'guests', 'prices'])
                ->orderBy('check_in')
                ->get(),
        ]);
    }

    /**
     * Cria uma nova reserva.
     */
    public function store(StoreReservationRequest $request, ReservationService $reservationService): JsonResponse
    {
        try {
            // Cria a reserva utilizando o service (regra de negócio)
            $reservation = $reservationService->create($request->validated());
        } catch (RoomUnavailableException $exception) {
            // Retorna erro caso o quarto não esteja disponível
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Reservation created successfully.',
            'data' => $reservation,
        ], 201);
    }

    /**
     * Exibe uma reserva específica com seus relacionamentos.
     */
    public function show(Reservation $reservation): JsonResponse
    {
        return response()->json([
            'data' => $reservation->load(['hotel', 'room', 'rate', 'guests', 'prices']),
        ]);
    }

    /**
     * Atualiza uma reserva existente.
     */
    public function update(
        UpdateReservationRequest $request,
        Reservation $reservation,
        ReservationService $reservationService
    ): JsonResponse {
        try {
            // Atualiza a reserva via service
            $reservation = $reservationService->update($reservation, $request->validated());
        } catch (RoomUnavailableException $exception) {
            // Retorna erro caso o quarto não esteja disponível
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Reservation updated successfully.',
            'data' => $reservation,
        ]);
    }

    /**
     * Remove uma reserva do sistema.
     */
    public function destroy(Reservation $reservation): Response
    {
        // Deleta a reserva
        $reservation->delete();

        // Retorna resposta sem conteúdo (padrão REST)
        return response()->noContent();
    }
}
