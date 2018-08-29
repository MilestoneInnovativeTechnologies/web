<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPresaleColumnsToRegTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
          $table->date("presale_enddate")->nullable()->after("edition");
					$table->date("presale_extended_to")->after("created_by")->nullable();
					$table->char("presale_extended_by",15)->after("presale_extended_to")->nullable();
					
					$table->foreign('presale_extended_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
					
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
            $table->dropForeign("customer_registrations_presale_extended_by_foreign");
            $table->dropColumn("presale_enddate");
            $table->dropColumn("presale_extended_to");
            $table->dropColumn("presale_extended_by");
        });
    }
}
