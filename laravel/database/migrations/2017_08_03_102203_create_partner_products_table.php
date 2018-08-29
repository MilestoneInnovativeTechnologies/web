<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_products', function (Blueprint $table) {
            $table->increments('id');
						$table->char("partner",15);
						$table->char("product",15)->nullable();
						$table->char("edition",15)->nullable();
						$table->char("created_by",15)->nullable();
            $table->timestamps();
						$table->enum("status",["ACTIVE","INACTIVE"])->default("ACTIVE");
					
						$table->unique(["partner","product","edition"]);
					
						$table->foreign('partner')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
						$table->foreign('product')->references('code')->on('products')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('edition')->references('code')->on('editions')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('partner_products');
    }
}
