<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequisitionFieldToCustomerRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
					DB::statement("ALTER TABLE customer_registrations MODIFY COLUMN presale_enddate DATE NULL AFTER created_by");
					DB::statement("ALTER TABLE customer_registrations MODIFY COLUMN lic_file VARCHAR(255) NULL AFTER created_by");
					DB::statement("ALTER TABLE customer_registrations MODIFY COLUMN serialno VARCHAR(60) NULL AFTER product_id");
          $table->string("requisition",15)->nullable()->after("database")->index();
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
            $table->dropColumn("requisition");
        });
    }
}
