<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payment_method')->default('on_site')->after('total_price')
                ->comment('on_site: 現地決済, credit_card: クレジットカード');
            $table->string('payment_status')->default('pending')->after('payment_method')
                ->comment('pending: 未払い, paid: 支払済み, failed: 失敗');
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'stripe_payment_intent_id']);
        });
    }
};
