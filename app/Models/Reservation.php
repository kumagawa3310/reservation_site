<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 修正ポイント：BelongsToをインポート

class Reservation extends Model
{
    use HasFactory; // 必要であれば（元のコードでコメントアウトされていなければ残します）

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
        'payment_method',
        'payment_status',
        'stripe_payment_intent_id',
        'status',
        'admin_memo',
    ];

    // --- リレーションの定義（修正ポイント） ---

    /**
     * @return BelongsTo<User, Reservation>
     */
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    /**
     * @return BelongsTo<StayPlan, Reservation>
     */
    public function stayPlan(): BelongsTo 
    { 
        return $this->belongsTo(StayPlan::class); 
    }

    /**
     * @return BelongsTo<Room, Reservation>
     */
    public function room(): BelongsTo 
    { 
        return $this->belongsTo(Room::class); 
    }


    /** 
     * Bladeなどで文字として表示したい場合に便利なメソッド
     * 
     * 修正ポイント：戻り値の型「: string」を指定
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => '予約確定',
            self::STATUS_CHECKED_IN => 'チェックイン済み',
            self::STATUS_CANCELLED => 'キャンセル',
            default => '不明',
        };
    }
}