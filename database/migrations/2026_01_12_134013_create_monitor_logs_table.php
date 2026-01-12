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
        Schema::create('monitor_logs', function (Blueprint $table) {
            $table->id(); // BigIncrements
            $table->uuid('monitor_id');
            $table->foreign('monitor_id')->references('id')->on('monitors')->onDelete('cascade');
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms');
            $table->boolean('is_success');
            $table->text('error_message')->nullable();
            $table->string('region')->default('default');
            $table->timestamp('created_at')->index();

            // No updated_at for logs (immutable)
            $table->index(['monitor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_logs');
    }
};
