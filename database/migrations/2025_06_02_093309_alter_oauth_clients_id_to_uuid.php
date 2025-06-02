<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterOauthClientsIdToUuid extends Migration
{
    public function up()
    {
        Schema::table('oauth_clients', function () {
            // Step 1: Remove AUTO_INCREMENT
            DB::statement('ALTER TABLE oauth_clients MODIFY COLUMN id INT');

            // Step 2: Drop the existing primary key
            DB::statement('ALTER TABLE oauth_clients DROP PRIMARY KEY');

            // Step 3: Change `id` column to CHAR(36)
            DB::statement('ALTER TABLE oauth_clients MODIFY COLUMN id CHAR(36)');

            // Step 4: Re-add the primary key to `id`
            DB::statement('ALTER TABLE oauth_clients ADD PRIMARY KEY (id)');
        });
    }

    public function down()
    {
        Schema::table('oauth_clients', function () {
            // Reverse steps

            // Step 1: Drop UUID primary key
            DB::statement('ALTER TABLE oauth_clients DROP PRIMARY KEY');

            // Step 2: Convert back to INT
            DB::statement('ALTER TABLE oauth_clients MODIFY COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT');

            // Step 3: Re-add primary key
            DB::statement('ALTER TABLE oauth_clients ADD PRIMARY KEY (id)');
        });
    }
}
