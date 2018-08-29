<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCustomerRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
            $table->string("lic_file")->nullable()->change();
						$table->string("product_id",60)->nullable()->after("serialno");
						$table->unsignedTinyInteger("seqno")->default(1)->after("customer")->change();
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
            $table->dropColumn("product_id");
        });
    }
}
