<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeatureCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_category', function (Blueprint $table) {
            $table->string('name',60)->default("Others")->primary();
            //$table->timestamps();
        });
				
				DB::table("feature_category")->insert(
					["name" => "Others"]
				);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_category');
    }
}
