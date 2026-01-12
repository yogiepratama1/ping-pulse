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
        Schema::table('monitor_logs', function (Blueprint $table) {
            $table->integer('avg_response_time_ms')->nullable()->after('response_time_ms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitor_logs', function (Blueprint $table) {
            $table->dropColumn('avg_response_time_ms');
        });
    }
};
