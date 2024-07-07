<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects_notifications_sent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('due');
            $table->boolean('overdue');
            $table->boolean('completed');
            $table->timestamps();
        });

        Schema::create('projects_tasks_notifications_sent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('project_task_id')->constrained('projects_tasks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('due');
            $table->boolean('overdue');
            $table->boolean('completed');
            $table->timestamps();
        });

        Schema::create('issues_notifications_sent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('priority');
            $table->boolean('resolved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_notifications_sent');
        Schema::dropIfExists('projects_tasks_notifications_sent');
        Schema::dropIfExists('issues_notifications_sent');
    }
};
