<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KeyModificationsOnPackageVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			$S = ["package_versions_edition_foreign","edition"];
			DB::statement("ALTER TABLE `package_versions` DROP FOREIGN KEY `".$S[0]."`"); DB::statement("ALTER TABLE `package_versions`	ADD CONSTRAINT `".$S[0]."` FOREIGN KEY (`".$S[1]."`) REFERENCES `".$S[1]."s` (`code`) ON UPDATE CASCADE ON DELETE RESTRICT");
			DB::statement("ALTER TABLE `package_versions` CHANGE COLUMN `".$S[1]."` `".$S[1]."` CHAR(5) NOT NULL COLLATE 'utf8mb4_unicode_ci'");
			$S = ["package_versions_package_foreign","package"];
			DB::statement("ALTER TABLE `package_versions` DROP FOREIGN KEY `".$S[0]."`"); DB::statement("ALTER TABLE `package_versions`	ADD CONSTRAINT `".$S[0]."` FOREIGN KEY (`".$S[1]."`) REFERENCES `".$S[1]."s` (`code`) ON UPDATE CASCADE ON DELETE RESTRICT");
			DB::statement("ALTER TABLE `package_versions` CHANGE COLUMN `".$S[1]."` `".$S[1]."` CHAR(5) NOT NULL COLLATE 'utf8mb4_unicode_ci'");
			$S = ["package_versions_product_foreign","product"];
			DB::statement("ALTER TABLE `package_versions` DROP FOREIGN KEY `".$S[0]."`"); DB::statement("ALTER TABLE `package_versions`	ADD CONSTRAINT `".$S[0]."` FOREIGN KEY (`".$S[1]."`) REFERENCES `".$S[1]."s` (`code`) ON UPDATE CASCADE ON DELETE RESTRICT");
			DB::statement("ALTER TABLE `package_versions` CHANGE COLUMN `".$S[1]."` `".$S[1]."` CHAR(5) NOT NULL COLLATE 'utf8mb4_unicode_ci'");
			DB::statement("ALTER TABLE `package_versions` ADD PRIMARY KEY (`product`,`edition`,`package`,`version_sequence`)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
