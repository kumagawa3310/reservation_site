<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * 修正ポイント：PHPDocでジェネリクス（<相手のモデル, 自分のモデル>）を指定
     *
     * @return BelongsTo<Room, SlotGenerationRule>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function effectivePrice(): int
    {
        return $this->price_override ?? $this->room?->price ?? 0;
    }
}