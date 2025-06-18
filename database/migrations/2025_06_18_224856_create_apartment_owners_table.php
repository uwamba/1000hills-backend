<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apartment_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('contract_path')->nullable();
            $table->string('status')->default('active');
            // assuming you have a users table for created_by / updated_by:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // If you want foreign key constraints (optional):
           
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartment_owners');
    }
};
