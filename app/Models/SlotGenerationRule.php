<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotGenerationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'start_date', 'end_date', 'price_override', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];


    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function effectivePrice(): int
    {
        return $this->price_override ?? $this->room->price;
    }
}
