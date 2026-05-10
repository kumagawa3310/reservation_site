<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('reservation_slots', function (Blueprint $table) {
        $table->id();
        // roomsテーブルとの紐付け
        $table->foreignId('room_id')->constrained()->onDelete('cascade');
        
        // どの日付の枠か
        $table->date('date');
        
        // 状態（available: 空き, reserved: 予約済み）
        // サイト外予約で枠を消す場合は、statusを変更するか、レコード自体を削除します
        $table->string('status')->default('available');
        
        // オプション：その日限りの特別料金を設定したい場合に便利
        $table->integer('price_override')->nullable(); 

        $table->timestamps();

        // 検索効率を上げるためのインデックス
        $table->index(['room_id', 'date']);
    });
}

/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_slots');
    }
};
