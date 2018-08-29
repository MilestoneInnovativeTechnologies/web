<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->char('user',15)->index();
            $table->unsignedInteger('user_time')->default("0");
						$table->char('supportteam',15)->index()->nullable();
            $table->string('message',360)->nullable();
						$table->char('responder',15)->index()->nullable();
						$table->string('role',50)->nullable();
						$table->string('response',360)->nullable();
						$table->unsignedInteger('time')->default("0");
						$table->char('ticket',15)->index()->nullable();
						$table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->timestamps();
						
						$table->foreign('user')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('supportteam')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('responder')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
}
