<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskRespondersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_responders', function (Blueprint $table) {
					$table->increments('id');
					$table->char('ticket',15)->index();
					$table->unsignedInteger('task')->index();
					$table->char('responder',15)->nullable()->index();
					$table->char('assigned_by',15)->nullable()->index();
					$table->timestamps();

					$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('task')->references('id')->on('ticket_tasks')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('responder')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('assigned_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_responders');
    }
}
