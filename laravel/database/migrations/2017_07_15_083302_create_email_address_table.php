<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_address', function (Blueprint $table) {
            $table->increments('id');
						$table->string("email",60)->unique();
						$table->string("type",45)->index()->nullable();
            $table->timestamps();
						$table->enum("isdeleted",["0","1"])->default("0");					
						$table->foreign('type')->references('name')->on('email_address_types')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_address');
    }
}
