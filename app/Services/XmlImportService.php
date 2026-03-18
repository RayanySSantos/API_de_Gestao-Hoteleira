<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Rate;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SimpleXMLElement;

class XmlImportService
{
    /**
     * @return array<string, int>
     */
    public function importAll(): array
    {
        return DB::transaction(function (): array {
            $counts = [
                'hotels' => $this->importHotels(),
                'rooms' => $this->importRooms(),
                'rates' => $this->importRates(),
                'reservations' => $this->importReservations(),
            ];

            $counts['total'] = array_sum($counts);

            return $counts;
        });
    }

    protected function importHotels(): int
    {
        $xml = $this->loadXml('hotels.xml');
        $items = [];

        foreach ($xml->hotel as $hotelNode) {
            $items[] = [
                'id' => (int) $hotelNode['id'],
                'name' => trim((string) $hotelNode->name),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Hotel::query()->upsert($items, ['id'], ['name', 'updated_at']);

        return count($items);
    }

    protected function importRooms(): int
    {
        $xml = $this->loadXml('rooms.xml');
        $items = [];

        foreach ($xml->room as $roomNode) {
            $items[] = [
                'id' => (int) $roomNode['id'],
                'hotel_id' => (int) $roomNode['hotel_id'],
                'name' => trim((string) $roomNode),
                'hotel_name' => $this->nullableString((string) $roomNode['hotel_name']),
                'inventory_count' => (int) $roomNode['inventory_count'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Room::query()->upsert(
            $items,
            ['id'],
            ['hotel_id', 'name', 'hotel_name', 'inventory_count', 'updated_at']
        );

        return count($items);
    }

    protected function importRates(): int
    {
        $xml = $this->loadXml('rates.xml');
        $items = [];

        foreach ($xml->rate as $rateNode) {
            $items[] = [
                'id' => (int) $rateNode['id'],
                'hotel_id' => (int) $rateNode['hotel_id'],
                'name' => trim((string) $rateNode),
                'hotel_name' => $this->nullableString((string) $rateNode['hotel_name']),
                'active' => ((string) $rateNode['active']) === '1',
                'price' => (float) $rateNode['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Rate::query()->upsert(
            $items,
            ['id'],
            ['hotel_id', 'name', 'hotel_name', 'active', 'price', 'updated_at']
        );

        return count($items);
    }

    protected function importReservations(): int
    {
        $xml = $this->loadXml('reservations.xml');
        $items = [];
        $reservationIds = [];

        foreach ($xml->reservation as $reservationNode) {
            $prices = iterator_to_array($reservationNode->room->price);
            $lastPrice = end($prices) ?: null;
            $reservationIds[] = (int) $reservationNode->id;

            $items[] = [
                'id' => (int) $reservationNode->id,
                'hotel_id' => (int) $reservationNode->hotel_id,
                'room_id' => (int) $reservationNode->room->id,
                'rate_id' => $lastPrice ? (int) $lastPrice['rate_id'] : null,
                'room_reservation_id' => (int) $reservationNode->room->roomreservation_id,
                'customer_first_name' => trim((string) $reservationNode->customer->first_name),
                'customer_last_name' => trim((string) $reservationNode->customer->last_name),
                'reservation_date' => (string) $reservationNode->date,
                'reservation_time' => (string) $reservationNode->time,
                'check_in' => (string) $reservationNode->room->arrival_date,
                'check_out' => (string) $reservationNode->room->departure_date,
                'currency_code' => (string) $reservationNode->room->currencycode ?: 'BRL',
                'meal_plan' => $this->nullableString((string) $reservationNode->room->meal_plan),
                'total_price' => (float) $reservationNode->room->totalprice,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Reservation::query()->upsert(
            $items,
            ['id'],
            [
                'hotel_id',
                'room_id',
                'rate_id',
                'room_reservation_id',
                'customer_first_name',
                'customer_last_name',
                'reservation_date',
                'reservation_time',
                'check_in',
                'check_out',
                'currency_code',
                'meal_plan',
                'total_price',
                'updated_at',
            ]
        );

        Reservation::query()
            ->whereIn('id', $reservationIds)
            ->with(['guests', 'prices'])
            ->get()
            ->each(function (Reservation $reservation): void {
                $reservation->guests()->delete();
                $reservation->prices()->delete();
            });

        foreach ($xml->reservation as $reservationNode) {
            /** @var Reservation $reservation */
            $reservation = Reservation::query()->findOrFail((int) $reservationNode->id);

            foreach ($reservationNode->room->guest_counts->guest_count as $guestNode) {
                $reservation->guests()->create([
                    'type' => (string) $guestNode['type'],
                    'count' => (int) $guestNode['count'],
                ]);
            }

            foreach ($reservationNode->room->price as $priceNode) {
                $reservation->prices()->create([
                    'rate_id' => isset($priceNode['rate_id']) ? (int) $priceNode['rate_id'] : null,
                    'price_date' => (string) $priceNode['date'],
                    'amount' => (float) $priceNode,
                ]);
            }
        }

        return count($items);
    }

    protected function loadXml(string $filename): SimpleXMLElement
    {
        $path = dirname(base_path()).DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.$filename;

        if (! file_exists($path)) {
            throw new RuntimeException("XML file not found: {$filename}");
        }

        $xml = simplexml_load_file($path);

        if ($xml === false) {
            throw new RuntimeException("Unable to parse XML file: {$filename}");
        }

        return $xml;
    }

    protected function nullableString(string $value): ?string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
