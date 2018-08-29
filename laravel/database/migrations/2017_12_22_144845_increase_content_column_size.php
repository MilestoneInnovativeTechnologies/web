<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseContentColumnSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_coversations', function (Blueprint $table) {
					DB::statement("ALTER TABLE `ticket_coversations` CHANGE COLUMN `content` `content` VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `type`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_coversations', function (Blueprint $table) {
            //
        });
    }
}
