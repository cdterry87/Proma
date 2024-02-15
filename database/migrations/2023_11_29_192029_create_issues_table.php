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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->smallInteger('priority');
            $table->date('resolve_date')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
