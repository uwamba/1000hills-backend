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
        Schema::table('buses', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_on')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn(['status', 'updated_by', 'deleted_by', 'deleted_on']);
        });
    }
    
};
