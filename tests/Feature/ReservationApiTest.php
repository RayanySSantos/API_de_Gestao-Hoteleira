<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_blocks_an_overlapping_reservation_for_the_same_room(): void
    {
        $this->postJson('/api/import')->assertOk();

        $response = $this->postJson('/api/reservations', [
            'room_id' => 137598802,
            'hotel_id' => 1375988,
            'rate_id' => 5333849,
            'customer_first_name' => 'Maria',
            'customer_last_name' => 'Silva',
            'check_in' => '2026-04-11',
            'check_out' => '2026-04-13',
            'currency_code' => 'BRL',
            'meal_plan' => 'Breakfast included.',
            'total_price' => 300.00,
            'guests' => [
                ['type' => 'adult', 'count' => 2],
            ],
            'prices' => [
                ['rate_id' => 5333849, 'price_date' => '2026-04-11', 'amount' => 150.00],
                ['rate_id' => 5333849, 'price_date' => '2026-04-12', 'amount' => 150.00],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'The room is unavailable for the selected period.');
    }

    public function test_it_creates_a_reservation_when_the_period_is_available(): void
    {
        $this->postJson('/api/import')->assertOk();

        $response = $this->postJson('/api/reservations', [
            'room_id' => 137598802,
            'hotel_id' => 1375988,
            'rate_id' => 5333849,
            'customer_first_name' => 'Julia',
            'customer_last_name' => 'Lima',
            'check_in' => '2026-04-12',
            'check_out' => '2026-04-14',
            'currency_code' => 'BRL',
            'meal_plan' => 'Breakfast included.',
            'total_price' => 300.00,
            'guests' => [
                ['type' => 'adult', 'count' => 2],
            ],
            'prices' => [
                ['rate_id' => 5333849, 'price_date' => '2026-04-12', 'amount' => 150.00],
                ['rate_id' => 5333849, 'price_date' => '2026-04-13', 'amount' => 150.00],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.customer_first_name', 'Julia')
            ->assertJsonPath('data.room_id', 137598802);

        $this->assertDatabaseHas('reservations', [
            'id' => $response->json('data.id'),
            'customer_first_name' => 'Julia',
            'room_id' => 137598802,
        ]);
    }

    public function test_it_returns_room_availability(): void
    {
        $this->postJson('/api/import')->assertOk();

        $this->getJson('/api/rooms/137598802/availability?check_in=2026-04-10&check_out=2026-04-12')
            ->assertOk()
            ->assertJsonPath('data.available', false);

        $this->getJson('/api/rooms/137598802/availability?check_in=2026-04-12&check_out=2026-04-14')
            ->assertOk()
            ->assertJsonPath('data.available', true);
    }
}
