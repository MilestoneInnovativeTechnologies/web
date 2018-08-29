<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address', function (Blueprint $table) {
            $table->increments('id');
						$table->string("buildingno")->nullable();
						$table->string("buildingname")->nullable();
						$table->string("streetno")->nullable();
						$table->string("streetname")->nullable();
						$table->string("landmark")->nullable();
						$table->string("area")->nullable();
						$table->string("place")->nullable();
						$table->string("city")->nullable();
						$table->string("state")->nullable();
						$table->string("country")->nullable();
						$table->string("zipcode")->nullable();
						$table->string("type",45)->index()->nullable();
            $table->timestamps();
						$table->enum("isdeleted",["0","1"])->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address');
    }
}
