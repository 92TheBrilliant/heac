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
        Schema::table('pages', function (Blueprint $table) {
            // Convert translatable fields to JSON to support multiple languages
            // The Spatie Translatable package stores translations as JSON
            // Fields: title, content, excerpt, meta_title, meta_description
            
            // Note: These fields are already defined in the original migration
            // The package will automatically handle JSON storage for translatable fields
            // No schema changes needed - the package uses the existing columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // No changes needed as we're using existing columns
        });
    }
};
