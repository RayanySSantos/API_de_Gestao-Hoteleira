<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Define se o usuário está autorizado a realizar esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação para atualização de um quarto.
     *
     * Utiliza "sometimes" para permitir atualização parcial dos dados.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Hotel ao qual o quarto pertence (valida apenas se enviado)
            'hotel_id' => ['sometimes', 'integer', 'exists:hotels,id'],

            // Nome do quarto
            'name' => ['sometimes', 'string', 'max:255'],

            // Nome do hotel (opcional, pode ser nulo)
            'hotel_name' => ['nullable', 'string', 'max:255'],

            // Quantidade disponível de quartos
            'inventory_count' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}