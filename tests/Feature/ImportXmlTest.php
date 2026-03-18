<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Rate;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportXmlTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_all_xml_files_into_the_database(): void
    {
        $response = $this->postJson('/api/import');

        $response
            ->assertOk()
            ->assertJsonPath('data.hotels', 3)
            ->assertJsonPath('data.rooms', 3)
            ->assertJsonPath('data.rates', 3)
            ->assertJsonPath('data.reservations', 6);

        $this->assertDatabaseCount('hotels', 3);
        $this->assertDatabaseCount('rooms', 3);
        $this->assertDatabaseCount('rates', 3);
        $this->assertDatabaseCount('reservations', 6);
        $this->assertDatabaseCount('reservation_guests', 6);
        $this->assertDatabaseCount('reservation_prices', 13);

        $this->assertTrue(Hotel::query()->whereKey(1375988)->exists());
        $this->assertTrue(Room::query()->whereKey(137598802)->exists());
        $this->assertTrue(Rate::query()->whereKey(5333849)->exists());
        $this->assertTrue(Reservation::query()->whereKey(3820212524)->exists());
    }
}
