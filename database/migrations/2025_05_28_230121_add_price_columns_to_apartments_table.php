<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->decimal('price_per_night', 10, 2)->default(0.00);
            $table->decimal('price_per_month', 10, 2)->default(0.00);
        });
    }

    public function down()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['price_per_night', 'price_per_month']);
        });
    }
};
