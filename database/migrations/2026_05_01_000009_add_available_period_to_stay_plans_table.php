<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stay_plans', function (Blueprint $table) {
            $table->date('available_from')->nullable()->comment('プラン開始日')->after('description');
            $table->date('available_to')->nullable()->comment('プラン終了日')->after('available_from');
        });
    }

    public function down(): void
    {
        Schema::table('stay_plans', function (Blueprint $table) {
            $table->dropColumn(['available_from', 'available_to']);
        });
    }
};
