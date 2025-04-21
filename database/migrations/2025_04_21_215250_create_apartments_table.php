<?php
// database/migrations/xxxx_xx_xx_create_apartments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('number_of_bedroom');
            $table->boolean('kitchen_inside')->default(false);
            $table->boolean('kitchen_outside')->default(false);
            $table->unsignedInteger('number_of_floor');
            $table->string('address');
            $table->string('coordinate')->nullable();
            $table->text('annexes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartments');
    }
}
