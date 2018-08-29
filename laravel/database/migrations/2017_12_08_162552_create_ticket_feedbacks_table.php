<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_feedbacks', function (Blueprint $table) {
					$table->increments('id');
					$table->char('ticket',15)->index();
					$table->char('customer',15)->index()->nullable();
					$table->unsignedInteger('points')->default(0);
					$table->text('feedback')->nullable();
					$table->timestamps();

					$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
				});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_feedbacks');
    }
}
