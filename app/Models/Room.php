<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'hotel_id', // ID do hotel ao qual o quarto pertence
        'name',
        'hotel_name',
        'inventory_count', // Quantidade disponível desse tipo de quarto
    ];

    protected function casts(): array
    {
        return [
            'inventory_count' => 'integer',
        ];
    }

    /**
     * Relação Many-to-One.
     * O quarto pertence a um único hotel.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /* Relação One-to-Many.
     * Um quarto pode possuir várias reservas ao longo do tempo.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
