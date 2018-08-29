<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdPartyApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_applications', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',100)->index();
            $table->string('description',1000)->nullable();
            $table->string('version',30)->nullable();
            $table->string('vendor_url',255)->nullable();
            $table->string('file',255)->nullable();
						$table->char('extension',5)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->enum('public',['Yes','No'])->default('Yes');
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->char('created_by',15)->index()->nullable();
            $table->timestamps();
						
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
        Schema::dropIfExists('third_party_applications');
    }
}
