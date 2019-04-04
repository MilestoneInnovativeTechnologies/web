<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmartSaleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smart_sale_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('smart_sale')->index();
            $table->string('table',160)->index();
            $table->unsignedSmallInteger('sync_to_ttl')->default(60);
            $table->unsignedSmallInteger('sync_from_ttl')->default(60);
            $table->dateTime('last_created')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->timestamps();
            $table->foreign('smart_sale')->references('id')->on('smart_sales')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smart_sale_tables');
    }
}
