<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFileds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_registrations', function (Blueprint $table) {
            $table->string('processor')->nullable()->after('database');
            $table->string('hard_disk')->nullable()->after('database');
            $table->string('os')->nullable()->after('database');
            $table->string('computer')->nullable()->after('database');
            $table->date('installed_on')->nullable()->after('database');
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
