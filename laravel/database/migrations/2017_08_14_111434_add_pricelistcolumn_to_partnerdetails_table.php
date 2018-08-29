<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPricelistcolumnToPartnerdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_details', function (Blueprint $table) {
					
					$table->char("pricelist",15)->nullable()->after("industry");
          $table->foreign("pricelist")->references("code")->on("price_lists")->onUpdate("cascade")->onDelete("set null");
					
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
						$table->dropForeign('partner_details_pricelist_foreign');
            $table->dropColumn("pricelist");
        });
    }
}
