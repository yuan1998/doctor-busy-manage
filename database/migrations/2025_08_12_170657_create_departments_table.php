<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string("name")->comment("科室名称");
            $table->string("description")->nullable()->comment("科室描述");
            $table->boolean('enable')->default(1)->comment("是否启用");
            $table->boolean("has_room")->default(0)->comment("是否有手术室");
            $table->json("rooms")->nullable();
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
        Schema::dropIfExists('departments');
    }
};
