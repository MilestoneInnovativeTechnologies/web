<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketCurrentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_current_status', function (Blueprint $table) {
            $table->increments('id');
						$table->char('ticket',15)->index();
						$table->enum('status',['NEW','OPENED','INPROGRESS','CLOSED','COMPLETED','HOLD','REOPENED'])->default('NEW');
						$table->text('status_text')->nullable();
						$table->unsignedInteger('start_time')->default(0);
						$table->unsignedInteger('end_time')->default(0);
						$table->unsignedInteger('total')->default(0);
						$table->char('user',15)->nullable()->index();
            $table->timestamps();

						$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('user')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_current_status');
    }
}
