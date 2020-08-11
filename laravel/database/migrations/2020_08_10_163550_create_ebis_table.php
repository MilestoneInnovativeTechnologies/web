<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEBisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ebis', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code',6)->index();
            $table->char('customer',15)->index();
            $table->unsignedTinyInteger('seq')->default(1)->index();
            $table->string('product',120)->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ebis');
    }
}
