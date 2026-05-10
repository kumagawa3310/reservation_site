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
        Schema::table('slot_generation_rules', function (Blueprint $table) {
            $table->dropColumn('days_ahead');
            $table->date('start_date')->after('room_id');
            $table->date('end_date')->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slot_generation_rules', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
            $table->unsignedInteger('days_ahead')->default(60)->after('room_id');
        });
    }
};
