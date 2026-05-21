<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // 修正ポイント：正しいHasManyをインポート

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 'name', 'capacity', 'number_of_rooms', 'price', 'description', 'is_active',
    ];

    /**
     * 修正ポイント：相手のモデル「ReservationSlot」のみを指定する
     *
     * @return HasMany<ReservationSlot>
     */
    public function reservationSlots(): HasMany
    {
        return $this->hasMany(ReservationSlot::class);
    }
}