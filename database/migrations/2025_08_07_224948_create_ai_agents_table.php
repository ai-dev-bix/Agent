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
        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('system_prompt');
            $table->string('model', 50)->default('gpt-4');
            $table->decimal('temperature', 3, 2)->default(0.7);
            $table->decimal('top_p', 3, 2)->default(1.0);
            $table->decimal('frequency_penalty', 3, 2)->default(0.0);
            $table->decimal('presence_penalty', 3, 2)->default(0.0);
            $table->integer('max_tokens')->default(2000);
            $table->string('avatar')->nullable();
            $table->text('welcome_message')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('output_language', 10)->default('en');
            $table->string('tone', 50)->default('professional');
            $table->string('writing_style', 50)->default('clear');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['is_public', 'is_active']);
            $table->index(['category']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_agents');
    }
};
