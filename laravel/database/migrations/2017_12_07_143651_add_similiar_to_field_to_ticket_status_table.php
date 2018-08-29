<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSimiliarToFieldToTicketStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_statuses', function (Blueprint $table) {
					$table->char('similiar_to_status',15)->index()->nullable()->after("after");
					$table->foreign('similiar_to_status')->references('code')->on('ticket_statuses')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_statuses', function (Blueprint $table) {
            //
        });
    }
}
