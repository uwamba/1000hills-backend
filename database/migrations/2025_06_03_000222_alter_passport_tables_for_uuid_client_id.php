<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->char('client_id', 36)->change();
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->char('client_id', 36)->change();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->char('id', 36)->change(); // if you didn't do this already
        });

        Schema::table('oauth_personal_access_clients', function (Blueprint $table) {
            $table->char('client_id', 36)->change();
        });
    }

    public function down(): void
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->change();
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->change();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->increments('id')->change(); // default back to int
        });

        Schema::table('oauth_personal_access_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->change();
        });
    }
};

