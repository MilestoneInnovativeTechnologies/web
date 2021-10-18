<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client')->index();
            $table->string('name',128)->index();
            $table->string('code',32)->index();
            $table->unsignedInteger('edition')->index()->nullable();
            $table->date('date')->index()->nullable();
            $table->string('serial',128)->index()->nullable();
            $table->string('key',128)->nullable();
            $table->string('ip_address',128)->nullable();
            $table->dateTime('ip_address_date')->nullable();
            $table->string('hostname',128)->nullable();
            $table->string('db_port',6)->default('3306')->nullable();
            $table->string('db_username',128)->nullable();
            $table->string('db_password',128)->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
            $table->foreign('client')->references('id')->on('sk_clients')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sk_branches');
    }
}
