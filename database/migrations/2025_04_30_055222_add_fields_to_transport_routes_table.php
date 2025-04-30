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
        Schema::table('transport_routes', function (Blueprint $table) {
            $table->string('status')->nullable()->after('price');
            $table->unsignedBigInteger('updated_by')->nullable()->after('status');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            $table->timestamp('deleted_on')->nullable()->after('deleted_by');

            // Optional: Add foreign key constraints if users table exists
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_routes', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['status', 'updated_by', 'deleted_by', 'deleted_on']);
        });
    }
};

