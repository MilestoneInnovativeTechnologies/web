<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCountryFromDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_details', function (Blueprint $table) {
						$table->dropForeign("partner_details_country_foreign");
            $table->dropColumn("country");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_details', function (Blueprint $table) {
            $table->unsignedInteger("country")->after("state")->nullable();
						$table->foreign("country")->on("countries")->references("id")->onUpdate("cascade")->onDelete("cascade");
        });
    }
}
