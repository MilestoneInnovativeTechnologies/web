<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactNumberTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_number_types', function (Blueprint $table) {
            $table->string('name',45)->primary();
        });
				
				DB::table("contact_number_types")->insert([
					["name"	=>	"Home"],
					["name"	=>	"Work"],
					["name"	=>	"Main"],
					["name"	=>	"Other"],
					["name"	=>	"Fax"]
					
				]);

		}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_number_types');
    }
}
