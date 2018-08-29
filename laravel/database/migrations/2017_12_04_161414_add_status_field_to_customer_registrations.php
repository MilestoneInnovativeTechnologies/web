<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFieldToCustomerRegistrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
            $table->enum('status',['ACTIVE','UPGRADED','DEGRADED'])->default('ACTIVE')->after("presale_extended_by");
            $table->string('remarks',100)->nullable()->after("status");
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
            $table->dropColumn("status");
            $table->dropColumn("remarks");
        });
    }
}
