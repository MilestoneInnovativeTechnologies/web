<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySsTableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smart_sale_tables', function (Blueprint $table) {
            $table->dateTime('record')->after('table')->nullable();
            $table->dateTime('sync')->after('table')->nullable();
            $table->unsignedMediumInteger('delay')->after('table')->default(60);
            $table->enum('type',['up','down','both'])->after('table');

            $table->dropColumn(['sync_to_ttl','sync_from_ttl','last_created','last_updated']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smart_sale_tables', function (Blueprint $table) {
            $table->dropColumn(['type','delay','sync','record']);
            $table->dateTime('last_updated')->after('table')->nullable();
            $table->dateTime('last_created')->after('table')->nullable();
            $table->unsignedSmallInteger('sync_from_ttl')->after('table')->default(0);
            $table->unsignedSmallInteger('sync_to_ttl')->after('table')->default(0);
        });
    }
}
