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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique(); // 部屋番号 (例: 101, 305)
            $table->string('name');                 // 部屋名 (例: スタンダードツイン)
            $table->integer('capacity');            // 収容人数
            $table->integer('price');               // 基本宿泊価格
            $table->text('description')->nullable(); // 説明文
            $table->boolean('is_active')->default(true); // 稼働状況
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
