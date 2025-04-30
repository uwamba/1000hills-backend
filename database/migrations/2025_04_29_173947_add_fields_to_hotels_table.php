<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('status')->nullable();         // New field
            $table->unsignedBigInteger('updated_by')->nullable();  // New field
            $table->unsignedBigInteger('deleted_by')->nullable();  // New field
            $table->timestamp('deleted_on')->nullable();  // New field

            // You can set foreign keys if necessary for 'updated_by' and 'deleted_by' 
            // (assuming you have a 'users' table for reference)
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);          // Drop foreign key
            $table->dropForeign(['deleted_by']);          // Drop foreign key

            $table->dropColumn(['status', 'updated_by', 'deleted_by', 'deleted_on']);  // Drop new fields
        });
    }
};