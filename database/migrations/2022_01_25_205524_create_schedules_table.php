<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->time('start_hour');
            $table->time('end_hour');
            $table->enum('type', ['unique', 'periodic']);
            $table->date('date')->nullable();
            $table->tinyInteger('day_of_week')->nullable();
            $table->text('google_event_id')->nullable();
            $table->unsignedBigInteger('monitor_id');
            $table->foreign('monitor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('dependency_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('schedules');
    }
}
