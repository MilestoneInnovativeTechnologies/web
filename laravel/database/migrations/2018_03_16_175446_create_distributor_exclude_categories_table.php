<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorExcludeCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_exclude_categories', function (Blueprint $table) {
            $table->increments('id');
						$table->char('distributor',15)->index();
						$table->char('category',15)->index();
						$table->char('created_by',15)->index()->nullable();
            $table->timestamps();
					
						$table->foreign('distributor')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('category')->references('code')->on('ticket_category_masters')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('created_by')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_exclude_categories');
    }
}
