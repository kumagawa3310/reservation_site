<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'body', 
        'status'
    ];

    /**
     * 修正ポイント：戻り値の型「: string」を指定
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            0 => '未対応',
            1 => '対応中',
            2 => '完了',
            default => '不明',
        };
    }
}