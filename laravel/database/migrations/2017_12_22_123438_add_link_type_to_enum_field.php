<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkTypeToEnumField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_coversations', function (Blueprint $table) {
            DB::statement("ALTER TABLE `ticket_coversations` CHANGE COLUMN `type` `type` ENUM('CHAT','FILE','INFO','LINK') NOT NULL DEFAULT 'CHAT' COLLATE 'utf8mb4_unicode_ci' AFTER `user`");
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
