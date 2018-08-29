<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationFieldsToAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('address', function (Blueprint $table) {
					
						DB::statement("ALTER TABLE `address` CHANGE `city` `city` INT(10) UNSIGNED DEFAULT NULL, ADD CONSTRAINT `FK_address_cities` FOREIGN KEY (`city`) REFERENCES `cities` (`id`) ON UPDATE CASCADE ON DELETE SET NULL;");
						DB::statement("ALTER TABLE `address` CHANGE `state` `state` INT(10) UNSIGNED DEFAULT NULL, ADD CONSTRAINT `FK_address_states` FOREIGN KEY (`state`) REFERENCES `states` (`id`) ON UPDATE CASCADE ON DELETE SET NULL;");
						DB::statement("ALTER TABLE `address` CHANGE `country` `country` INT(10) UNSIGNED DEFAULT NULL, ADD CONSTRAINT `FK_address_countries` FOREIGN KEY (`country`) REFERENCES `countries` (`id`) ON UPDATE CASCADE ON DELETE SET NULL;");
            
						//$table->integer("city")->nullable()->unsigned()->index()->change();
					  //$table->integer("state")->nullable()->unsigned()->index()->change();
					  //$table->integer("country")->nullable()->unsigned()->index()->change();
					
						//$table->foreign("city")->references("id")->on("cities")->onUpdate("cascade")->onDelete("set null");
						//$table->foreign("state")->references("id")->on("states")->onUpdate("cascade")->onDelete("set null");
						//$table->foreign("country")->references("id")->on("countries")->onUpdate("cascade")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('address', function (Blueprint $table) {
           
        });
    }
}
