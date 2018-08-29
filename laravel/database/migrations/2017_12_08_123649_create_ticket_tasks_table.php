<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->char('ticket',15)->nullable()->index();
            $table->unsignedTinyInteger('seqno')->default(1);
            $table->string('title',100)->index();
            $table->text('description')->nullable();
            $table->char('support_type',15)->index()->nullable();
						$table->unsignedTinyInteger('weightage')->default(100);
						$table->UnsignedInteger('handle_after')->index()->nullable();
						$table->char('created_by',15)->index()->nullable();
            $table->timestamps();

						$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('support_type')->references('code')->on('support_types')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('handle_after')->references('id')->on('ticket_tasks')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('created_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_tasks');
    }
}
