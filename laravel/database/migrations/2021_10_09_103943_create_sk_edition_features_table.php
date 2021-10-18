<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkEditionFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_edition_features', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('edition')->index();
            $table->unsignedInteger('feature')->index();
            $table->string('value',256)->nullable();
            $table->timestamps();
            $table->foreign('edition')->references('id')->on('sk_editions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('feature')->references('id')->on('sk_features')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sk_edition_features');
    }
}
