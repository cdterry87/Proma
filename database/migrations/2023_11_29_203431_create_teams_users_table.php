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
        Schema::create('teams_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('manager')->default(false);
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams_users');
    }
};
