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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('status')->default('active')->after('phone');
            $table->unsignedBigInteger('updated_by')->nullable()->after('status');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            $table->timestamp('deleted_on')->nullable()->after('deleted_by');

            // Optional: Add foreign key constraints if users table exists
            // $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            // $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['status', 'updated_by', 'deleted_by', 'deleted_on']);
        });
    }
};