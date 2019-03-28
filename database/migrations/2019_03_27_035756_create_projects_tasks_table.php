<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('complete')->default(false);
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
        Schema::dropIfExists('projects_tasks');
    }
}
