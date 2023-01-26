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
        Schema::create('competency_job_title', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->foreignId('job_title_id')->references('id')->on('job_titles');
            $table->foreignId('competency_id')->references('id')->on('competencies');
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
        Schema::dropIfExists('competency_job_title');
    }
};
