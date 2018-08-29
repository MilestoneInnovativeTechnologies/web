<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByColumnToRequiredTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
		protected $Array = [
			["actions","description"],
			["customer_industry","name"],
			["customer_registrations","key"],
			["editions","description_public"],
			["features","description_public"],
			["feature_category","name"],
			["packages","description_public"],
			["package_versions","status_reason"],
			["partners","status_description"],
			["presale_customers","extended_to"],
			["products","description_public"],
			["products_editions","description"],
			["products_features","order"],
			["product_edition_features","order"],
			["product_edition_packages","package"],
			["resources","description"],
			["roles","status"],
			["role_resource","action"],					
		];
	
		protected $All = 18;
	
    public function up()
    {
			foreach($this->Array as $K => $Obj){
				$Status = DB::statement("ALTER TABLE `".$Obj[0]."` ADD COLUMN `created_by` CHAR(15) NULL AFTER `".$Obj[1]."`, ADD CONSTRAINT `".$Obj[0]."_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `partners` (`code`) ON UPDATE CASCADE ON DELETE SET NULL");
				if(!$Status) { $this->All = $K; return $this->down(); }
			}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
			foreach($this->Array as $K => $Obj){
				if($K >= $this->All) return;
				DB::statement("ALTER TABLE `".$Obj[0]."` DROP COLUMN `created_by`, DROP FOREIGN KEY `".$Obj[0]."_created_by_foreign`");
			}
    }
}
