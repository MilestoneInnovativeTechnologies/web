<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecreatedStatusToTicketStatusTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_status_trans', function (Blueprint $table) {
            DB::statement("ALTER TABLE `ticket_status_trans` CHANGE COLUMN `status` `status` ENUM('NEW','OPENED','INPROGRESS','CLOSED','COMPLETED','HOLD','REOPENED','RECREATED') NOT NULL DEFAULT 'NEW' COLLATE 'utf8mb4_unicode_ci' AFTER `ticket`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_status_trans', function (Blueprint $table) {
            //
        });
    }
}
