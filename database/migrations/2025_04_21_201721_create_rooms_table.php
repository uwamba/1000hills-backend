<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->boolean('has_wireless')->default(false);
            $table->string('bed_size');
            $table->boolean('has_bathroom')->default(false);
            $table->decimal('price', 8, 2); // Price as a decimal (with two decimal points)
            $table->string('currency');
            $table->integer('number_of_people');
            $table->boolean('has_ac')->default(false);
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade'); // Foreign key for hotel
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
