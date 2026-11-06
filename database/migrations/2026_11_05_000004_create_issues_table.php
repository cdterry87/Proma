<?php

declare(strict_types=1);

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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->index();
            $table->text('description');
            $table->unsignedSmallInteger('priority');
            $table->date('resolved_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'resolved_date']);
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
