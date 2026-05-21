<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 修正ポイント：BelongsToをインポート

class ReservationSlot extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'date', 'status', 'price', 'price_override'];

    /**
     * 修正ポイント：PHPDocでジェネリクス（相手のモデル）を指定
     *
     * @return BelongsTo<Room, ReservationSlot>
     */
    public function room(): BelongsTo // 修正ポイント：戻り値の型を指定
    {
        return $this->belongsTo(Room::class);
    }
}