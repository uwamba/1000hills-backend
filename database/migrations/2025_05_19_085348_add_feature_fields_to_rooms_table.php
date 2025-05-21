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
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('has_swimming_pool')->default(false);
            $table->boolean('has_laundry')->default(false);
            $table->boolean('has_gym')->default(false);
            $table->boolean('has_room_service')->default(false);
            $table->boolean('has_sauna_massage')->default(false);
            $table->boolean('has_kitchen')->default(false);
            $table->boolean('has_fridge')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('rooms', function (Blueprint $table) {
        $table->dropColumn([
            'has_swimming_pool',
            'has_laundry',
            'has_gym',
            'has_room_service',
            'has_sauna_massage',
            'has_kitchen',
            'has_fridge',
        ]);
    });
}
};
