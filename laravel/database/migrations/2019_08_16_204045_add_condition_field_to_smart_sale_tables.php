<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConditionFieldToSmartSaleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smart_sale_tables', function (Blueprint $table) {
            $table->string('condition',4096)->nullable()->after('delay');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smart_sale_tables', function (Blueprint $table) {
            $table->dropColumn(['condition']);
        });
    }
}
