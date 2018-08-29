<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PresaleAddAdditionalColumns extends Migration
{
			public function __construct()
			{
					DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
			}
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presale_customers', function (Blueprint $table) {
          $table->unsignedTinyInteger("seqno")->after("partner")->default("1");
          $table->char("product",15)->nullable()->after("seqno");
					$table->char("edition",15)->nullable()->after("product");
					$table->char("modified_by",15)->nullable()->after("created_by");

					$table->date("startdate")->nullable()->change();
					$table->date("enddate")->nullable()->change();
					$table->date("extended_to")->nullable()->change();
					
					$table->foreign('product')->references('code')->on('products')->onUpdate('cascade')->onDelete('restrict');
					$table->foreign('edition')->references('code')->on('editions')->onUpdate('cascade')->onDelete('restrict');
					$table->foreign('modified_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('restrict');
					
        });
			
			DB::unprepared("
			CREATE TRIGGER `presale_customers_before_insert` BEFORE INSERT ON `presale_customers` FOR EACH ROW BEGIN
				DECLARE V_PRODUCT CHAR(15);
				DECLARE V_EDITION CHAR(15);
				SELECT product INTO V_PRODUCT FROM customer_registrations WHERE customer = NEW.partner AND seqno = NEW.seqno;
				SELECT edition INTO V_EDITION FROM customer_registrations WHERE customer = NEW.partner AND seqno = NEW.seqno;
				SET NEW.product = V_PRODUCT;
				SET NEW.edition = V_EDITION;
			END;
			"); 
			
			DB::unprepared("
			CREATE TRIGGER `presale_customers_before_update` BEFORE UPDATE ON `presale_customers` FOR EACH ROW BEGIN
				DECLARE V_PRODUCT CHAR(15);
				DECLARE V_EDITION CHAR(15);
				SELECT product INTO V_PRODUCT FROM customer_registrations WHERE customer = NEW.partner AND seqno = NEW.seqno;
				SELECT edition INTO V_EDITION FROM customer_registrations WHERE customer = NEW.partner AND seqno = NEW.seqno;
				SET NEW.product = V_PRODUCT;
				SET NEW.edition = V_EDITION;
			END;
			"); /**/
			
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presale_customers', function (Blueprint $table) {
            $table->dropColumn("seqno");
            $table->dropColumn("product");
            $table->dropColumn("edition");
            $table->dropColumn("modified_by");
					
						$table->dropForeign("presale_customers_product_foreign");
						$table->dropForeign("presale_customers_edition_foreign");
						$table->dropForeign("presale_customers_modified_by_foreign");
        });
    }
}
