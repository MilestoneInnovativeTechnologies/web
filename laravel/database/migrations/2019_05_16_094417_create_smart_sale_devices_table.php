<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmartSaleDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smart_sale_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('smart_sale')->index();
            $table->string('name',160)->index()->nullable();
            $table->string('uuid',100)->index()->nullable();
            $table->string('imei',32)->index()->nullable();
            $table->string('serial',32)->index()->nullable();
            $table->string('code1',32)->index()->nullable();
            $table->string('code2',32)->index()->nullable();
            $table->string('code3',32)->index()->nullable();
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
        Schema::dropIfExists('smart_sale_devices');
    }
}
