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
        Schema::create('competency_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->references('id')->on('peoples');
            $table->foreignId('competency_id')->references('id')->on('competencies');
            $table->smallInteger('grade');
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
        Schema::dropIfExists('competency_people');
    }
};
