<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->timestamp('from_date_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('to_date_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->morphs('object');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_to_pay', 10, 2);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
