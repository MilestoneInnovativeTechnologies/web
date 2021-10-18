<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateSkEditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_editions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->index();
            $table->string('detail',4096)->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
        });

        DB::table('sk_editions')->insert(['id' => 1,'name' => 'Custom','detail' => 'Features are added or removed according to client requirement','status' => 'Active','created_at' => Carbon::now()->toDateTimeString(),'updated_at' => Carbon::now()->toDateTimeString()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sk_editions');
    }
}
