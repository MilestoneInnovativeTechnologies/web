<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brandings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',120)->nullable();
            $table->string('icon',320)->nullable();
            $table->string('heading',120)->nullable();
            $table->string('caption',120)->nullable();
            $table->string('color_scheme',15)->default('188,94,56');
            $table->string('about',1000)->nullable();
            $table->string('address',400)->nullable();
            $table->string('email',400)->nullable();
            $table->string('number',400)->nullable();
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
        Schema::dropIfExists('brandings');
    }
}
