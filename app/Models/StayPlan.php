<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StayPlan extends Model
{
    use SoftDeletes; // 論理削除を有効化

    protected $table = 'stay_plans';

    protected $fillable = [
        'room_id',
        'name',
        'price',
        'description',
        'available_from',
        'available_to',
    ];

    protected $casts = [
        'available_from' => 'date',
        'available_to'   => 'date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** 今日の日付で有効なプランのみ絞り込む */
    public function scopeActive($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('available_from')->orWhere('available_from', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('available_to')->orWhere('available_to', '>=', $today);
        });
    }

    /** プランが現在有効かどうか */
    public function getIsActiveAttribute(): bool
    {
        $today = now()->startOfDay();

        if ($this->available_from && $this->available_from->gt($today)) {
            return false;
        }

        if ($this->available_to && $this->available_to->lt($today)) {
            return false;
        }

        return true;
    }
}