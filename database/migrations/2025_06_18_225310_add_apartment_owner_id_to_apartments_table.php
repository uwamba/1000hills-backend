<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('apartments', function (Blueprint $table) {
            // Add the column; adjust placement with after(...) if desired
            $table->unsignedBigInteger('apartment_owner_id')->nullable()->after('id');
            
            // Add foreign key constraint (assuming apartment_owners table exists)
            $table->foreign('apartment_owner_id')
                  ->references('id')
                  ->on('apartment_owners')
                  ->onDelete('set null'); 
                  // or onDelete('cascade') if you want to delete apartments when owner is deleted
        });
    }

    public function down()
    {
        Schema::table('apartments', function (Blueprint $table) {
            // Drop foreign key first, then column
            $table->dropForeign(['apartment_owner_id']);
            $table->dropColumn('apartment_owner_id');
        });
    }
};
