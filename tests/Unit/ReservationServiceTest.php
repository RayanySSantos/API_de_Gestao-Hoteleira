<?php

namespace Tests\Unit;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_overlapping_dates_for_the_same_room(): void
    {
        $hotel = Hotel::query()->create([
            'id' => 10,
            'name' => 'Hotel Unit',
        ]);

        $room = Room::query()->create([
            'id' => 20,
            'hotel_id' => $hotel->id,
            'name' => 'Quarto 20',
            'inventory_count' => 1,
        ]);

        Reservation::query()->create([
            'id' => 30,
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'customer_first_name' => 'Teste',
            'customer_last_name' => 'Base',
            'reservation_date' => '2026-03-17',
            'reservation_time' => '10:00:00',
            'check_in' => '2026-04-10',
            'check_out' => '2026-04-12',
            'currency_code' => 'BRL',
            'total_price' => 100,
        ]);

        $service = app(ReservationService::class);

        $this->assertFalse($service->isRoomAvailable($room, '2026-04-11', '2026-04-13'));
        $this->assertTrue($service->isRoomAvailable($room, '2026-04-12', '2026-04-14'));
    }
}
