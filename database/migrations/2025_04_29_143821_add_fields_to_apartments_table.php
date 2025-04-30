<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->string('status')->default('active');  // New field
            $table->unsignedBigInteger('updated_by')->nullable();  // New field
            $table->unsignedBigInteger('deleted_by')->nullable();  // New field
            $table->timestamp('deleted_on')->nullable();  // New field
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['status', 'updated_by', 'deleted_by', 'deleted_on']);
        });
    }
};
