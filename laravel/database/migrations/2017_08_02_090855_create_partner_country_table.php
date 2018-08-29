<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_countries', function (Blueprint $table) {
            $table->increments('id');
						$table->char("partner",15);
						$table->unsignedInteger("country");
            $table->foreign("partner")->on("partners")->references("code")->onUpdate("cascade")->onDelete("cascade");
						$table->foreign("country")->on("countries")->references("id")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_countries');
    }
}
