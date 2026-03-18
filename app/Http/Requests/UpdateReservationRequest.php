<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Reservation $reservation */
        $reservation = $this->route('reservation');

        return [
            'hotel_id' => ['sometimes', 'integer', 'exists:hotels,id'],
            'room_id' => ['sometimes', 'integer', 'exists:rooms,id'],
            'rate_id' => ['nullable', 'integer', 'exists:rates,id'],
            'room_reservation_id' => [
                'nullable',
                'integer',
                Rule::unique('reservations', 'room_reservation_id')->ignore($reservation->id),
            ],
            'customer_first_name' => ['sometimes', 'string', 'max:255'],
            'customer_last_name' => ['sometimes', 'string', 'max:255'],
            'reservation_date' => ['sometimes', 'date'],
            'reservation_time' => ['nullable', 'date_format:H:i:s'],
            'check_in' => ['sometimes', 'date', 'before:check_out'],
            'check_out' => ['sometimes', 'date', 'after:check_in'],
            'currency_code' => ['sometimes', 'string', 'size:3'],
            'meal_plan' => ['nullable', 'string', 'max:255'],
            'total_price' => ['sometimes', 'numeric', 'min:0'],
            'guests' => ['sometimes', 'array'],
            'guests.*.type' => ['required_with:guests', 'string', 'max:50'],
            'guests.*.count' => ['required_with:guests', 'integer', 'min:1'],
            'prices' => ['sometimes', 'array'],
            'prices.*.rate_id' => ['nullable', 'integer', 'exists:rates,id'],
            'prices.*.price_date' => ['required_with:prices', 'date'],
            'prices.*.amount' => ['required_with:prices', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('check_in') && ! $this->has('check_out')) {
            /** @var Reservation $reservation */
            $reservation = $this->route('reservation');
            $this->merge(['check_out' => $reservation->check_out->toDateString()]);
        }

        if ($this->has('check_out') && ! $this->has('check_in')) {
            /** @var Reservation $reservation */
            $reservation = $this->route('reservation');
            $this->merge(['check_in' => $reservation->check_in->toDateString()]);
        }
    }
}
