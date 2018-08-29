<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldlengthOfProductEdition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_editions', function (Blueprint $table) {
					DB::statement("ALTER TABLE `products_editions` CHANGE COLUMN `product` `product` CHAR(15) NOT NULL COLLATE 'utf8mb4_unicode_ci' FIRST");
					DB::statement("ALTER TABLE `products_editions` CHANGE COLUMN `edition` `edition` CHAR(15) NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `product`");
					DB::statement("ALTER TABLE `products_editions` CHANGE COLUMN `level` `level` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `edition`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_editions', function (Blueprint $table) {
            //
        });
    }
}
