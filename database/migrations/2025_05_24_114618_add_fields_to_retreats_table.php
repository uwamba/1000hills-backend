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
        Schema::table('retreats', function (Blueprint $table) {
            $table->string('type');
            $table->boolean('wifi');
            $table->boolean('projector');
            $table->boolean('theater');
            $table->boolean('flip_chart');
            $table->boolean('whiteboard');
            $table->string('pricing_type')->nullable();
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->decimal('package_price', 10, 2)->nullable();
            $table->integer('package_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retreats', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'wifi',
                'projector',
                'theater',
                'flip_chart',
                'whiteboard',
                'pricing_type',
                'price_per_person',
                'package_price',
                'package_size',
            ]);
        });
    }
};
