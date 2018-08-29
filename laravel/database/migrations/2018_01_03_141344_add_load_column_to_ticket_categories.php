<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoadColumnToTicketCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
					DB::statement("ALTER TABLE `ticket_categories` ADD COLUMN `load` VARCHAR(75) NULL DEFAULT 'ALWAYS' AFTER `priority`");
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
