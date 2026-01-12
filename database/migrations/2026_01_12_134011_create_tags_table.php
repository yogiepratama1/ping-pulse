<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->default('#3b82f6'); // Hex color for UI badges
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship
        Schema::create('monitor_tag', function (Blueprint $table) {
            $table->uuid('monitor_id');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->foreign('monitor_id')->references('id')->on('monitors')->onDelete('cascade');
            $table->primary(['monitor_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_tag');
        Schema::dropIfExists('tags');
    }
};
