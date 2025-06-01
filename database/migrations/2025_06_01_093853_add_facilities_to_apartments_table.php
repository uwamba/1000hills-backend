<?php

 use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacilitiesToApartmentsTable extends Migration
{
    public function up()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->boolean('swimming_pool')->default(false);
            $table->boolean('laundry')->default(false);
            $table->boolean('gym')->default(false);
            $table->boolean('room_service')->default(false);
            $table->boolean('sauna_massage')->default(false);
        });
    }

    public function down()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn([
                'swimming_pool',
                'laundry',
                'gym',
                'room_service',
                'sauna_massage',
            ]);
        });
    }
}
