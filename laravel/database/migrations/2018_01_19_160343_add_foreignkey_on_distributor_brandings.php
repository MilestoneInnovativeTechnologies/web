<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeyOnDistributorBrandings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			
				DB::statement("ALTER TABLE `distributor_brandings` CHANGE COLUMN `distributor` `distributor` CHAR(15) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `id`");
        Schema::table('distributor_brandings', function (Blueprint $table) {
            $table->foreign('branding')->references('id')->on('brandings')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('distributor')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
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
