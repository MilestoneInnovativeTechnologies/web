<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TicketTasksAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_tasks', function (Blueprint $table) {
					$table->enum('status',['ACTIVE','INACTIVE'])->index()->default('ACTIVE')->after("handle_after");
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
