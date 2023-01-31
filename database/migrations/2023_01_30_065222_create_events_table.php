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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('note')->nullable();

            $table->string('color')->default('#3788d8');

            $table->date('start_date')->index();
            $table->time('start_time')->nullable()->index();

            $table->date('end_date')->index();
            $table->time('end_time')->nullable()->index();

            $table->text('rrule')->nullable();

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
        Schema::dropIfExists('events');
    }
};
