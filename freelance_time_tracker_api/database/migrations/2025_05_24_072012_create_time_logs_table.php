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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('CASCADE');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->dateTime('start_time')->nullable(true);
            $table->dateTime('end_time')->nullable(true);
            $table->string('description');
            $table->float('hours')->nullable(true);
            $table->enum('tag', ['billable', 'non-billable'])->default('billable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
