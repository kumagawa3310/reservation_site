<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            
            // 外部キー
            $table->foreignId('user_id')->constrained()->onDelete('cascade');       
            $table->foreignId('stay_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');

            // 宿泊情報
            $table->date('check_in_date');
            $table->date('check_out_date');
            
            // 予約者情報
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            
            // 予約詳細
            $table->integer('number_of_guests');
            $table->integer('total_price');
            $table->tinyInteger('status')->default(1)->comment('1:予約確定, 2:チェックイン済, 3:キャンセル');

            $table->timestamps();
            
            // インデックス（重複予約チェックの高速化用）
            $table->index(['room_id', 'check_in_date', 'check_out_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};