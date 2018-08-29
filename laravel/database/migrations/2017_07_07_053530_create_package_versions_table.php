<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_versions', function (Blueprint $table) {
            $table->char('product',5)->nullable()->index();
            $table->char('edition',5)->nullable()->index();
            $table->char('package',5)->nullable()->index();
            $table->integer('version_sequence')->default(1);
            $table->smallInteger('major_version')->unsigned()->default(1);
            $table->smallInteger('minor_version')->unsigned()->default(0);
            $table->smallInteger('build_version')->unsigned()->default(0);
            $table->smallInteger('revision')->unsigned()->default(0);
            $table->string('version_string')->nullable();
            $table->timestamp('build_date')->nullable();
            $table->timestamp('deploy_date')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->text('change_log')->nullable();
            $table->text('bugs')->nullable();
            $table->text('file')->nullable();
            $table->enum('status',["PENDING","APPROVED","REJECTED","REVERTED","AWAITING UPLOAD"])->default("PENDING");
            $table->text('status_reason')->nullable();
            $table->timestamps();
						$table->foreign('product')->references('code')->on('products')->onDelete('set null')->onUpdate('cascade');
						$table->foreign('edition')->references('code')->on('editions')->onDelete('set null')->onUpdate('cascade');
						$table->foreign('package')->references('code')->on('packages')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_versions');
    }
}
