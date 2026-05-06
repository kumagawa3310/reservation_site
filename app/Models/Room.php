<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 'name', 'capacity', 'number_of_rooms', 'price', 'description', 'is_active',
    ];

    public function reservationSlots()
    {
        return $this->hasMany(ReservationSlot::class);
    }
}
