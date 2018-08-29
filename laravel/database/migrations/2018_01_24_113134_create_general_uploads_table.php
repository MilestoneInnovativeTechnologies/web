<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_uploads', function (Blueprint $table) {
            $table->char('code',15)->primary();
            $table->string('name',120)->nullable();
            $table->string('description',500)->nullable();
            $table->char('customer',15)->nullable()->index();
            $table->char('ticket',15)->nullable()->index();
            $table->string('file',500)->nullable();
            $table->unsignedInteger('size')->default(0);
            $table->unsignedInteger('time')->default(0);
            $table->enum('overwrite',['Y','N'])->default('N');
            $table->char('created_by',15)->nullable()->index();
            $table->enum('deleted',['Y','N'])->default('N');
            $table->timestamps();
					
						$table->foreign('customer')->references('code')->on('partners')->onUpdate('cascade')->onDelete('set null');
						$table->foreign('ticket')->references('code')->on('tickets')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('general_uploads');
    }
}
