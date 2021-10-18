<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch')->index();
            $table->unsignedInteger('edition')->index()->nullable();
            $table->string('remarks',512)->nullable();
            $table->dateTime('expiry')->index();
            $table->text('code')->nullable();
            $table->dateTime('code_date')->nullable();
            $table->enum('status',['Active','Expired','Cancelled'])->default('Active');
            $table->timestamps();
            $table->foreign('branch')->references('id')->on('sk_branches')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('edition')->references('id')->on('sk_editions')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sk_subscriptions');
    }
}
