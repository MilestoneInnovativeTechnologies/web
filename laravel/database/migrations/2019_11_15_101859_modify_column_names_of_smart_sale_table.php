<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnNamesOfSmartSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smart_sales', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE smart_sales CHANGE `print_head_line1` `print_line1` VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci'");
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE smart_sales CHANGE `print_head_line2` `print_line2` VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci'");
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE smart_sales CHANGE `footer_text` `print_line3` VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci'");
//            $table->renameColumn('print_head_line1', 'print_line1');
//            $table->renameColumn('print_head_line2', 'print_line2');
//            $table->renameColumn('footer_text', 'print_line3');
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
            //
        });
    }
}
