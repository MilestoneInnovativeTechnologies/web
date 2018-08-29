<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
				Schema::create('customer_industry', function (Blueprint $table) {
					
					$table->char('code',15)->primary();
					$table->string('name',60)->nullable();
					
				});
			
        Schema::create('partner_details', function (Blueprint $table) {
					
					$table->char('code',15)->primary();
					$table->char('partner',15)->nullable()->index();
					$table->string('address1',255)->nullable();
					$table->string('address2',255)->nullable();
					$table->integer("city")->unsigned()->nullable()->index();
					$table->integer("state")->unsigned()->nullable()->index();
					$table->integer("country")->unsigned()->nullable()->index();
					$table->char("industry",15)->nullable()->index();
					$table->string("currency",5)->nullable();
					$table->integer("phonecode")->nullable();
					$table->string("phone",15)->nullable();
					$table->string("website",100)->nullable();
					$table->enum("status",['ACTIVE','INACTIVE'])->default('ACTIVE');
					$table->timestamps();
					
					$table->foreign('partner')->references('code')->on('partners')->onUpdate('cascade')->onDelete('cascade');
					$table->foreign('city')->references('id')->on('cities')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('state')->references('id')->on('states')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('country')->references('id')->on('countries')->onUpdate('cascade')->onDelete('set null');
					$table->foreign('industry')->references('code')->on('customer_industry')->onUpdate('cascade')->onDelete('set null');
					
        });

		}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
			Schema::dropIfExists('partner_details');
			Schema::dropIfExists('customer_industry');
    }
}
