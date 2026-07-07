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
        Schema::table('dialer_logs', function (Blueprint $table) {
            $table->text('request_data')->nullable()->after('event');
            $table->text('note_details')->nullable()->after('request_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dialer_logs', function (Blueprint $table) {
            $table->dropColumn(['request_data', 'note_details']);
        });
    }
};
