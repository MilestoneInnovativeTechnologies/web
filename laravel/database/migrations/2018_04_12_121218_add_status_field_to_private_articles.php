<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFieldToPrivateArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_articles', function (Blueprint $table) {
            $table->enum('status',['ACTIVE','INACTIVE'])->default('Active')->after('target_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_articles', function (Blueprint $table) {
					$table->dropColumn('status');
        });
    }
}
