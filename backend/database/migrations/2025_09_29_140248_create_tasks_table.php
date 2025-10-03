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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('origin_id');
            $table->foreign('origin_id', 'tasks_origin_id_index')
                ->references('id')
                ->on('origins')
                ->onDelete('cascade');

            $table->uuid('destination_id');
            $table->foreign('destination_id', 'tasks_destination_id_index')
                ->references('id')
                ->on('destinations')
                ->onDelete('cascade');
            $table->string('schedule_expression')->nullable(); // Cron expression
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Prevent duplicate origin-destination combinations
            $table->unique(['origin_id', 'destination_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
