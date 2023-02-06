<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // extension to calculate distance
        \DB::statement("CREATE EXTENSION IF NOT EXISTS cube;");
        \DB::statement("CREATE EXTENSION IF NOT EXISTS earthdistance;");

        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("picture")->nullable();
            $table->string("address")->nullable();
            $table->float("longitude");
            $table->float("latitude");
            $table->foreignId("brand_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlets');
    }
}
