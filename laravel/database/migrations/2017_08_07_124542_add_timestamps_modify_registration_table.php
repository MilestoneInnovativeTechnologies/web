<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsModifyRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
            $table->timestamps();
						$table->date('registered_on')->nullable()->after("key");
						$table->dropColumn("date");
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
            //
        });
    }
}
