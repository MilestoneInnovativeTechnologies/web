<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pd', function (Blueprint $table) {
            $table->increments('id');
            $table->char('customer',15)->index()->nullable();
            $table->unsignedTinyInteger('seq')->default(1)->index();
            $table->char('code',6)->index()->nullable();
            $table->string('url_web','150')->nullable();
            $table->string('url_interact','150')->nullable();
            $table->string('url_api','150')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
            $table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pd');
    }
}
