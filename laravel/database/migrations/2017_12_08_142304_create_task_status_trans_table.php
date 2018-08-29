<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskStatusTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_status_trans', function (Blueprint $table) {
					$table->increments('id');
					$table->char('ticket',15)->index();
					$table->unsignedInteger('task')->index();
					$table->enum('status',['CREATED','ASSIGNED','ACTIVE','INACTIVE','OPENED','RECHECK','REASSIGNED','WORKING','HOLD','CLOSED'])->default('CREATED');
					$table->text('status_text')->nullable();
					$table->unsignedInteger('start_time')->default(0);
					$table->unsignedInteger('end_time')->default(0);
					$table->unsignedInteger('total')->default(0);
					$table->char('user',15)->nullable()->index();
					$table->timestamps();

					$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('task')->references('id')->on('ticket_tasks')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('task_status_trans');
    }
}
