<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionRefToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('transaction_ref')->nullable()->after('id');
            $table->string('payment_provider_id')->nullable()->after('transaction_ref');
            $table->enum('payment_status', ['pending', 'paid', 'cancelled', 'failed'])->default('pending')->after('payment_provider_id');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['transaction_ref', 'payment_provider_id', 'payment_status']);
        });
    }
}
