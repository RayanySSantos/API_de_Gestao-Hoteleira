<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_updates_and_deletes_a_room(): void
    {
        Hotel::query()->create([
            'id' => 1,
            'name' => 'Hotel Teste',
        ]);

        $createResponse = $this->postJson('/api/rooms', [
            'id' => 1001,
            'hotel_id' => 1,
            'name' => 'Suite Master',
            'hotel_name' => 'Hotel Teste',
            'inventory_count' => 3,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.id', 1001)
            ->assertJsonPath('data.name', 'Suite Master');

        $updateResponse = $this->putJson('/api/rooms/1001', [
            'name' => 'Suite Premium',
            'inventory_count' => 4,
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('data.name', 'Suite Premium')
            ->assertJsonPath('data.inventory_count', 4);

        $this->deleteJson('/api/rooms/1001')->assertNoContent();

        $this->assertDatabaseMissing('rooms', ['id' => 1001]);
    }
}
