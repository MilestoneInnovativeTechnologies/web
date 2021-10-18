<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->char('partner',15)->nullable()->index();
            $table->string('name',128)->index();
            $table->string('code',32)->index();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
            $table->foreign('partner')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sk_clients');
    }
}
