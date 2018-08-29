<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandingLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branding_links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand')->index();
            $table->string('link',150)->nullable();
            $table->string('name',150)->nullable();
            $table->string('fa',150)->nullable();
            $table->enum('target',['_blank','_self','_parent','_top'])->default('_blank');
            $table->timestamps();
					
						$table->foreign('brand')->references('id')->on('brandings')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branding_links');
    }
}
