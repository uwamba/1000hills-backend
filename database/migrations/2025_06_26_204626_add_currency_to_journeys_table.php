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
    Schema::table('journeys', function (Blueprint $table) {
        $table->string('currency', 10)->default('USD'); // or nullable()
    });
}

public function down()
{
    Schema::table('journeys', function (Blueprint $table) {
        $table->dropColumn('currency');
    });
}
};
