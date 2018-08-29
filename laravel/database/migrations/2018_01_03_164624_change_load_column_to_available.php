<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLoadColumnToAvailable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
					DB::statement("ALTER TABLE `ticket_categories` CHANGE COLUMN `load` `available` VARCHAR(75) NULL DEFAULT 'ALWAYS' COLLATE 'utf8mb4_unicode_ci' AFTER `priority`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            //
        });
    }
}
