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
        $table->string('from');
        $table->string('to')->after('from');
        $table->dateTime('departure')->after('to');
        $table->dateTime('return')->nullable()->after('departure');
        $table->foreignId('bus_id')->constrained('buses')->after('return');
    });
}

    /**
     * Reverse the migrations.
     */
   public function down()
{
    Schema::table('journeys', function (Blueprint $table) {
        $table->dropColumn(['from', 'to', 'departure', 'return']);
        $table->dropForeign(['bus_id']);
        $table->dropColumn('bus_id');
    });
}
};
