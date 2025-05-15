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
            if (!Schema::hasColumn('buses', 'status')) {
                $table->string('status')->nullable();
            }
            if (!Schema::hasColumn('buses', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
            if (!Schema::hasColumn('buses', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
            }
            if (!Schema::hasColumn('buses', 'deleted_on')) {
                $table->timestamp('deleted_on')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('buses', function (Blueprint $table) {
            if (Schema::hasColumn('buses', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('buses', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
            if (Schema::hasColumn('buses', 'deleted_by')) {
                $table->dropColumn('deleted_by');
            }
            if (Schema::hasColumn('buses', 'deleted_on')) {
                $table->dropColumn('deleted_on');
            }
        });
    }
};
