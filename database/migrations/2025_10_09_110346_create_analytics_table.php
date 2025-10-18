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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // page_view, research_view, research_download, etc.
            $table->string('trackable_type')->nullable(); // Model type (Page, Research, etc.)
            $table->unsignedBigInteger('trackable_id')->nullable(); // Model ID
            $table->string('url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for performance
            $table->index(['event_type', 'created_at']);
            $table->index(['trackable_type', 'trackable_id']);
            $table->index('created_at');
            $table->index('user_id');
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
