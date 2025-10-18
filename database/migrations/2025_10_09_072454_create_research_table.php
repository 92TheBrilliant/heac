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
        Schema::create('research', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('abstract')->nullable();
            $table->json('authors')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('slug');
            $table->index('status');
            $table->index('featured');
            $table->index('publication_date');
            
            // Full-text search index (MySQL)
            $table->fullText(['title', 'abstract']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research');
    }
};
