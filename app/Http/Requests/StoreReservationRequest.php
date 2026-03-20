<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Define se o usuário está autorizado a realizar esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação para criação de uma reserva.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Identificador opcional da reserva (caso venha de integração externa)
            'id' => ['nullable', 'integer', 'unique:reservations,id'],

            // Hotel opcional, mas deve existir se informado
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],

            // Quarto obrigatório e deve existir
            'room_id' => ['required', 'integer', 'exists:rooms,id'],

            // Tarifa opcional
            'rate_id' => ['nullable', 'integer', 'exists:rates,id'],

            // ID externo da reserva (integração)
            'room_reservation_id' => ['nullable', 'integer', 'unique:reservations,room_reservation_id'],

            // Dados do cliente
            'customer_first_name' => ['required', 'string', 'max:255'],
            'customer_last_name' => ['required', 'string', 'max:255'],

            // Data e hora da reserva
            'reservation_date' => ['nullable', 'date'],
            'reservation_time' => ['nullable', 'date_format:H:i:s'],

            // Datas de hospedagem
            'check_in' => ['required', 'date', 'before:check_out'],
            'check_out' => ['required', 'date', 'after:check_in'],

            // Código da moeda (ex: BRL, USD)
            'currency_code' => ['nullable', 'string', 'size:3'],

            // Plano de refeição
            'meal_plan' => ['nullable', 'string', 'max:255'],

            // Valor total da reserva
            'total_price' => ['required', 'numeric', 'min:0'],

            // Lista de hóspedes
            'guests' => ['sometimes', 'array'],
            'guests.*.type' => ['required_with:guests', 'string', 'max:50'],
            'guests.*.count' => ['required_with:guests', 'integer', 'min:1'],

            // Lista de preços por diária
            'prices' => ['sometimes', 'array'],
            'prices.*.rate_id' => ['nullable', 'integer', 'exists:rates,id'],
            'prices.*.price_date' => ['required_with:prices', 'date'],
            'prices.*.amount' => ['required_with:prices', 'numeric', 'min:0'],
        ];
    }
}