<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrintFieldsToSsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smart_sales', function (Blueprint $table) {
            $table->string('footer_text',400)->nullable()->after('brief');
            $table->string('print_head_line2',400)->nullable()->after('brief');
            $table->string('print_head_line1',400)->nullable()->after('brief');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smart_sales', function (Blueprint $table) {
            $table->dropColumn(['footer_text','print_head_line2','print_head_line1']);
        });
    }
}
