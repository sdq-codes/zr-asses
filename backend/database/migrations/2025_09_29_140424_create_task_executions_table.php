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
        Schema::create('task_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->foreign('task_id', 'tasks_execution_task_id_index')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('execution_time_ms')->nullable(); // Execution time in milliseconds
            $table->integer('urls_scraped')->default(0);
            $table->integer('urls_success')->default(0);
            $table->integer('urls_failed')->default(0);
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Store additional execution data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_executions');
    }
};
