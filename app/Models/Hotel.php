<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    /**
     * Campos permitidos para atribuição em massa.
     * Evita que o usuário envie dados indevidos para a model.
     */
    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * Relação 1:N.
     * Um hotel pode possuir vários quartos.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relação 1:N.
     * Um hotel pode possuir várias tarifas.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Relação 1:N.
     * Um hotel pode possuir várias reservas.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
