<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    /**
     * Define se o usuário está autorizado a realizar esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação para criação de um quarto.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Identificador do quarto (obrigatório e único)
            'id' => ['required', 'integer', 'unique:rooms,id'],

            // Hotel ao qual o quarto pertence (deve existir)
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],

            // Nome ou tipo do quarto
            'name' => ['required', 'string', 'max:255'],

            // Nome do hotel (campo opcional, geralmente para integração)
            'hotel_name' => ['nullable', 'string', 'max:255'],

            // Quantidade disponível desse tipo de quarto
            'inventory_count' => ['required', 'integer', 'min:1'],
        ];
    }
}