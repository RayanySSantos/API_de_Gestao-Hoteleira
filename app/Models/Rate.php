<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rate extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'hotel_id',
        'name',
        'hotel_name',
        'active',
        'price',
    ];

    /**
     * Define a conversão automática de atributos.
     * O campo price é formatado com duas casas decimais
     * e o campo active é convertido para boolean.
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Relação Many-to-One.
     * A tarifa pertence a um único hotel.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Relação 1:N.
     * Uma tarifa pode estar associada a várias reservas.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
