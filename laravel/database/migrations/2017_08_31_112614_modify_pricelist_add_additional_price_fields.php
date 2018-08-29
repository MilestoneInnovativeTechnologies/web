<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPricelistAddAdditionalPriceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_list_details', function (Blueprint $table) {
						$table->decimal('cost',30,10)->default(0)->after("edition");
						$table->decimal('mrp',30,10)->default(0)->after("price");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_list_details', function (Blueprint $table) {
            $table->dropColumn('cost');
            $table->dropColumn('mrp');
        });
    }
}
