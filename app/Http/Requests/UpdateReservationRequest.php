<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Define se o usuário está autorizado a realizar esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação para atualização de uma reserva.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Reservation $reservation */
        $reservation = $this->route('reservation');

        return [
            // IDs opcionais (validados apenas se enviados)
            'hotel_id' => ['sometimes', 'integer', 'exists:hotels,id'],
            'room_id' => ['sometimes', 'integer', 'exists:rooms,id'],
            'rate_id' => ['nullable', 'integer', 'exists:rates,id'],

            // ID externo único, ignorando o próprio registro
            'room_reservation_id' => [
                'nullable',
                'integer',
                Rule::unique('reservations', 'room_reservation_id')->ignore($reservation->id),
            ],

            // Dados do cliente
            'customer_first_name' => ['sometimes', 'string', 'max:255'],
            'customer_last_name' => ['sometimes', 'string', 'max:255'],

            // Datas e horários
            'reservation_date' => ['sometimes', 'date'],
            'reservation_time' => ['nullable', 'date_format:H:i:s'],

            // Datas de hospedagem
            'check_in' => ['sometimes', 'date', 'before:check_out'],
            'check_out' => ['sometimes', 'date', 'after:check_in'],

            // Dados adicionais
            'currency_code' => ['sometimes', 'string', 'size:3'],
            'meal_plan' => ['nullable', 'string', 'max:255'],
            'total_price' => ['sometimes', 'numeric', 'min:0'],

            // Lista de hóspedes
            'guests' => ['sometimes', 'array'],
            'guests.*.type' => ['required_with:guests', 'string', 'max:50'],
            'guests.*.count' => ['required_with:guests', 'integer', 'min:1'],

            // Lista de preços
            'prices' => ['sometimes', 'array'],
            'prices.*.rate_id' => ['nullable', 'integer', 'exists:rates,id'],
            'prices.*.price_date' => ['required_with:prices', 'date'],
            'prices.*.amount' => ['required_with:prices', 'numeric', 'min:0'],
        ];
    }

    /**
     * Prepara os dados antes da validação.
     * Garante que check_in e check_out estejam sempre presentes para validação correta.
     */
    protected function prepareForValidation(): void
    {
        // Se enviou apenas check_in, mantém o check_out atual
        if ($this->has('check_in') && ! $this->has('check_out')) {
            /** @var Reservation $reservation */
            $reservation = $this->route('reservation');

            $this->merge([
                'check_out' => $reservation->check_out->toDateString()
            ]);
        }

        // Se enviou apenas check_out, mantém o check_in atual
        if ($this->has('check_out') && ! $this->has('check_in')) {
            /** @var Reservation $reservation */
            $reservation = $this->route('reservation');

            $this->merge([
                'check_in' => $reservation->check_in->toDateString()
            ]);
        }
    }
}