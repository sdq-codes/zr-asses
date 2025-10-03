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
        Schema::create('scraped_apartments', function (Blueprint $table) {
            $table->uuid('id')->primary()->default();
            $table->uuid('task_id');
            $table->foreign('task_id', 'scraped_apartments_task_id_index')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');
            $table->string('origin'); // e.g. Zillow, Redfin
            $table->json('urls');     // store scraped apartment URLs
            $table->integer('count')->default(0);
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();     // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraped_apartments');
    }
};
