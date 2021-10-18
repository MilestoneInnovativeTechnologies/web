<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkBranchFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_branch_features', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch')->index();
            $table->unsignedInteger('feature')->index();
            $table->string('value',256)->nullable();
            $table->foreign('branch')->references('id')->on('sk_branches')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sk_branch_features');
    }
}
