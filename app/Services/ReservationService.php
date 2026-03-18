<?php

namespace App\Services;

use App\Exceptions\RoomUnavailableException;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function isRoomAvailable(Room $room, string $checkIn, string $checkOut, ?int $ignoreReservationId = null): bool
    {
        return ! Reservation::query()
            ->where('room_id', $room->id)
            ->when($ignoreReservationId !== null, fn ($query) => $query->where('id', '!=', $ignoreReservationId))
            ->whereRaw('date(check_in) < ?', [$checkOut])
            ->whereRaw('date(check_out) > ?', [$checkIn])
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Reservation
    {
        /** @var Room $room */
        $room = Room::query()->findOrFail($data['room_id']);

        if (! $this->isRoomAvailable($room, $data['check_in'], $data['check_out'])) {
            throw new RoomUnavailableException('The room is unavailable for the selected period.');
        }

        return DB::transaction(function () use ($data, $room): Reservation {
            $reservation = Reservation::query()->create([
                'id' => $data['id'] ?? $this->nextReservationId(),
                'hotel_id' => $data['hotel_id'] ?? $room->hotel_id,
                'room_id' => $room->id,
                'rate_id' => $data['rate_id'] ?? null,
                'room_reservation_id' => $data['room_reservation_id'] ?? $this->nextRoomReservationId(),
                'customer_first_name' => $data['customer_first_name'],
                'customer_last_name' => $data['customer_last_name'],
                'reservation_date' => $data['reservation_date'] ?? now()->toDateString(),
                'reservation_time' => $data['reservation_time'] ?? now()->format('H:i:s'),
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'currency_code' => $data['currency_code'] ?? 'BRL',
                'meal_plan' => $data['meal_plan'] ?? null,
                'total_price' => $data['total_price'],
            ]);

            foreach ($data['guests'] ?? [] as $guest) {
                $reservation->guests()->create($guest);
            }

            foreach ($data['prices'] ?? [] as $price) {
                $reservation->prices()->create($price);
            }

            return $reservation->load(['hotel', 'room', 'rate', 'guests', 'prices']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Reservation $reservation, array $data): Reservation
    {
        $roomId = $data['room_id'] ?? $reservation->room_id;
        /** @var Room $room */
        $room = Room::query()->findOrFail($roomId);

        $checkIn = $data['check_in'] ?? $reservation->check_in->toDateString();
        $checkOut = $data['check_out'] ?? $reservation->check_out->toDateString();

        if (! $this->isRoomAvailable($room, $checkIn, $checkOut, $reservation->id)) {
            throw new RoomUnavailableException('The room is unavailable for the selected period.');
        }

        return DB::transaction(function () use ($reservation, $data, $room, $checkIn, $checkOut): Reservation {
            $reservation->update([
                'hotel_id' => $data['hotel_id'] ?? $room->hotel_id,
                'room_id' => $room->id,
                'rate_id' => $data['rate_id'] ?? $reservation->rate_id,
                'room_reservation_id' => $data['room_reservation_id'] ?? $reservation->room_reservation_id,
                'customer_first_name' => $data['customer_first_name'] ?? $reservation->customer_first_name,
                'customer_last_name' => $data['customer_last_name'] ?? $reservation->customer_last_name,
                'reservation_date' => $data['reservation_date'] ?? $reservation->reservation_date->toDateString(),
                'reservation_time' => $data['reservation_time'] ?? $reservation->reservation_time,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'currency_code' => $data['currency_code'] ?? $reservation->currency_code,
                'meal_plan' => array_key_exists('meal_plan', $data) ? $data['meal_plan'] : $reservation->meal_plan,
                'total_price' => $data['total_price'] ?? $reservation->total_price,
            ]);

            if (array_key_exists('guests', $data)) {
                $reservation->guests()->delete();

                foreach ($data['guests'] ?? [] as $guest) {
                    $reservation->guests()->create($guest);
                }
            }

            if (array_key_exists('prices', $data)) {
                $reservation->prices()->delete();

                foreach ($data['prices'] ?? [] as $price) {
                    $reservation->prices()->create($price);
                }
            }

            return $reservation->load(['hotel', 'room', 'rate', 'guests', 'prices']);
        });
    }

    protected function nextReservationId(): int
    {
        return ((int) Reservation::query()->max('id')) + 1;
    }

    protected function nextRoomReservationId(): int
    {
        return ((int) Reservation::query()->max('room_reservation_id')) + 1;
    }
}
