<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeHandleAfterColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_tasks', function (Blueprint $table) {
					$table->dropForeign(['handle_after']);
					$table->dropIndex(['handle_after']);
					//$table->string('handle_after', 255)->change()->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_tasks', function (Blueprint $table) {
            //
        });
    }
}
