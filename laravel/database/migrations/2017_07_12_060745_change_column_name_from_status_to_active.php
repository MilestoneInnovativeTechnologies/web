<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnNameFromStatusToActive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `products` CHANGE COLUMN `status` `active` ENUM('1','0') NOT NULL DEFAULT '1'");
        DB::statement("ALTER TABLE `editions` CHANGE COLUMN `status` `active` ENUM('1','0') NOT NULL DEFAULT '1'");
        DB::statement("ALTER TABLE `packages` CHANGE COLUMN `status` `active` ENUM('1','0') NOT NULL DEFAULT '1'");
        DB::statement("ALTER TABLE `features` CHANGE COLUMN `status` `active` ENUM('1','0') NOT NULL DEFAULT '1'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `products` CHANGE COLUMN `active` `status` ENUM('1','0') NOT NULL DEFAULT '1'");
        DB::statement("ALTER TABLE `editions` CHANGE COLUMN `active` `status` ENUM('1','0') NOT NULL DEFAULT '1'");
        DB::statement("ALTER TABLE `packages` CHANGE COLUMN `active` `status` ENUM('1','0') NOT NULL DEFAULT '1'");
        DB::statement("ALTER TABLE `features` CHANGE COLUMN `active` `status` ENUM('1','0') NOT NULL DEFAULT '1'");
    }
}
