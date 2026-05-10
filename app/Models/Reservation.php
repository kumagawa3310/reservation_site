<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
// ステータスの定数定義
    const STATUS_CONFIRMED = 1;  // 予約確定
    const STATUS_CHECKED_IN = 2; // チェックイン済み
    const STATUS_CANCELLED = 3;  // キャンセル

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'stay_plan_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'guest_name',
        'guest_email',
        'guest_phone',
        'number_of_guests',
        'total_price',
        'status',
        'admin_memo',
    ];

    // リレーションの定義
    public function user() { return $this->belongsTo(User::class); }
    public function stayPlan() { return $this->belongsTo(StayPlan::class); }
    public function room() { return $this->belongsTo(Room::class); }

    // Bladeなどで文字として表示したい場合に便利なメソッド
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => '予約確定',
            self::STATUS_CHECKED_IN => 'チェックイン済み',
            self::STATUS_CANCELLED => 'キャンセル',
            default => '不明',
        };
    }
}
