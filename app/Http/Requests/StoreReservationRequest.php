<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
        return [
            'id' => ['nullable', 'integer', 'unique:reservations,id'],
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'rate_id' => ['nullable', 'integer', 'exists:rates,id'],
            'room_reservation_id' => ['nullable', 'integer', 'unique:reservations,room_reservation_id'],
            'customer_first_name' => ['required', 'string', 'max:255'],
            'customer_last_name' => ['required', 'string', 'max:255'],
            'reservation_date' => ['nullable', 'date'],
            'reservation_time' => ['nullable', 'date_format:H:i:s'],
            'check_in' => ['required', 'date', 'before:check_out'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'meal_plan' => ['nullable', 'string', 'max:255'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'guests' => ['sometimes', 'array'],
            'guests.*.type' => ['required_with:guests', 'string', 'max:50'],
            'guests.*.count' => ['required_with:guests', 'integer', 'min:1'],
            'prices' => ['sometimes', 'array'],
            'prices.*.rate_id' => ['nullable', 'integer', 'exists:rates,id'],
            'prices.*.price_date' => ['required_with:prices', 'date'],
            'prices.*.amount' => ['required_with:prices', 'numeric', 'min:0'],
        ];
    }
}
