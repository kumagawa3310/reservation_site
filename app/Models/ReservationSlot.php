<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSlot extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'date', 'status', 'price', 'price_override'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
