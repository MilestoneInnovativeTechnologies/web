<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColumnToTableDistributorBrandings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributor_brandings', function (Blueprint $table) {
					DB::statement("ALTER TABLE `distributor_brandings` ADD COLUMN `type` ENUM('company','product') NOT NULL DEFAULT 'company' AFTER `domain`");
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distributor_brandings', function (Blueprint $table) {
            //
        });
    }
}
