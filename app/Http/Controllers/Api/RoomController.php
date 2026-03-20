<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    /**
     * Lista todos os quartos com seus respectivos hotéis.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Room::query()->with('hotel')->orderBy('id')->get(),
        ]);
    }

    /**
     * Cria um novo quarto.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = Room::query()->create($request->validated())->load('hotel');

        return response()->json([
            'message' => 'Room created successfully.',
            'data' => $room,
        ], 201);
    }

    /**
     * Exibe um quarto específico com seus relacionamentos.
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json([
            'data' => $room->load(['hotel', 'reservations']),
        ]);
    }

    /**
     * Exibe um quarto específico com seus relacionamentos.
     */
    public function update(UpdateRoomRequest $request, Room $room): JsonResponse
    {
        $room->update($request->validated());

        return response()->json([
            'message' => 'Room updated successfully.',
            'data' => $room->load('hotel'),
        ]);
    }

    /**
     * Remove um quarto do sistema.
     */
    public function destroy(Room $room): Response
    {
        $room->delete();

        return response()->noContent();
    }
}
