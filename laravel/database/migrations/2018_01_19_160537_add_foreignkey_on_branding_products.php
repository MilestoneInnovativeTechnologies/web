<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeyOnBrandingProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branding_products', function (Blueprint $table) {
            $table->foreign('product')->references('code')->on('products')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('edition')->references('code')->on('editions')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branding_products', function (Blueprint $table) {
            //
        });
    }
}
