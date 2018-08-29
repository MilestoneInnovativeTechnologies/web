<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToAllMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `products` ADD `status` ENUM('1','0') DEFAULT '1'");
        DB::statement("ALTER TABLE `editions` ADD `status` ENUM('1','0') DEFAULT '1'");
        DB::statement("ALTER TABLE `features` ADD `status` ENUM('1','0') DEFAULT '1'");
        DB::statement("ALTER TABLE `packages` ADD `status` ENUM('1','0') DEFAULT '1'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `products` DROP COLUMN `status`");
        DB::statement("ALTER TABLE `editions` DROP COLUMN `status`");
        DB::statement("ALTER TABLE `features` DROP COLUMN `status`");
        DB::statement("ALTER TABLE `packages` DROP COLUMN `status`");
    }
}
