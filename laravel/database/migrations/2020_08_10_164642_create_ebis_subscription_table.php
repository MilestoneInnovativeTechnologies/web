<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEbisSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ebis_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code',6)->index();
            $table->enum('package',['Basic'])->index()->default('Basic');
            $table->date('start')->index()->nullable();
            $table->date('end')->index()->nullable();
            $table->string('domain',64)->nullable();
            $table->enum('status',['New','Upcoming','Active','Expired','Inactive','Cancelled'])->index()->default('New');
            $table->timestamps();
            $table->foreign('code')->references('code')->on('ebis')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ebis_subscriptions');
    }
}
