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
        if (!Schema::hasColumn('leads', 'is_dnc')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->tinyInteger('is_dnc')->default(0)->after('status')->index();
            });
        }

        if (!Schema::hasTable('campaign_dnc_companies')) {
            Schema::create('campaign_dnc_companies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('source_id')->index();
                $table->string('company_name')->index();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->unique(['source_id', 'company_name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_dnc_companies');

        if (Schema::hasColumn('leads', 'is_dnc')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('is_dnc');
            });
        }
    }
};

