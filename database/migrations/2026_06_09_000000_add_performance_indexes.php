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
        Schema::table('leads', function (Blueprint $table) {
            $table->index(['status', 'asign_to']);
            $table->index('asign_to');
            $table->index('status');
        });
        Schema::table('lhs_report', function (Blueprint $table) {
            $table->index('lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['status', 'asign_to']);
            $table->dropIndex(['asign_to']);
            $table->dropIndex(['status']);
        });
        Schema::table('lhs_report', function (Blueprint $table) {
            $table->dropIndex(['lead_id']);
        });
    }
};
