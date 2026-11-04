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
        Schema::create('progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_id')->constrained()->restrictOnDelete();
            $table->decimal('progress_percentage', 5, 2);
            $table->text('notes')->nullable();
            $table->boolean('is_correction')->default(false);
            $table->date('recorded_at');
            $table->timestamps();

            $table->index(['project_schedule_id', 'recorded_at', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_updates');
    }
};