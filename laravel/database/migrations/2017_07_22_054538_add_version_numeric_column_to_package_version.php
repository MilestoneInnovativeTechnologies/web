<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVersionNumericColumnToPackageVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_versions', function (Blueprint $table) {
            $table->string("version_numeric",20)->nullable()->after("version_string");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_versions', function (Blueprint $table) {
            $table->dropColumn("version_numeric");
        });
    }
}
