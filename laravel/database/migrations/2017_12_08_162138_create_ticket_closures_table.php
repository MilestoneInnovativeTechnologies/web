<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_closures', function (Blueprint $table) {
					$table->increments('id');
					$table->char('ticket',15)->index();
					$table->text('solution')->nullable();
					$table->text('support_doc')->nullable();
					$table->char('reference_ticket',15)->index()->nullable();
					$table->timestamps();

					$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('reference_ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_closures');
    }
}
