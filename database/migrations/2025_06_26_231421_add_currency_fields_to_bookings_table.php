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
       Schema::table('bookings', function (Blueprint $table) {
        $table->string('currency_code')->nullable();
        $table->decimal('currency_rate_to_usd', 12, 6)->nullable();
        $table->string('payment_method')->nullable();
        $table->text('extra_note')->nullable();
        $table->string('momo_number')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn([
            'currency_code',
            'currency_rate_to_usd',
            'payment_method',
            'extra_note',
            'momo_number'
        ]);
    });
    }
};
