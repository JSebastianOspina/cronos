<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('monitor_id');
            $table->foreign('monitor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('dependency_id')->constrained()->cascadeOnDelete();
            $table->timestamp('start_planned_date');
            $table->timestamp('end_planned_date');
            $table->timestamp('start_monitor_date')->nullable();
            $table->timestamp('end_monitor_date')->nullable();
            $table->timestamp('start_approved_date')->nullable();
            $table->timestamp('end_approved_date')->nullable();
            $table->timestamp('total_planned_minutes')->nullable();
            $table->timestamp('total_monitor_minutes')->nullable();
            $table->timestamp('total_approved_minutes')->nullable();
            $table->enum('status', ['created', 'in_process', 'finished', 'approved'])->nullable();
            $table->json('observation')->nullable();
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
        Schema::dropIfExists('records');
    }
}
