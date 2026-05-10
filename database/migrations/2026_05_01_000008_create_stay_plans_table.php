<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stay_plans', function (Blueprint $table) {
            $table->id();
            // roomsテーブルのIDと紐付け。外部キー制約。
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('name')->comment('プラン名');
            $table->integer('price')->comment('料金');
            $table->text('description')->nullable()->comment('説明文');
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (論理削除用)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};