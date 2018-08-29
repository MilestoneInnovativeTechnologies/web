<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailAddressTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_address_types', function (Blueprint $table) {
            $table->string('name',45)->primary();
        });
				
				DB::table("email_address_types")->insert([
					["name"	=>	"Home"],
					["name"	=>	"Work"],
					["name"	=>	"Other"]
				]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_address_types');
    }
}
