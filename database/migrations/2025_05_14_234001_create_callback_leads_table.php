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
        Schema::create('callback_leads', function (Blueprint $table) {
            $table->id();
            $table->integer('note_id')->nullable();
            $table->integer('lead_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->integer('status')->default(0)->comment('0:pending,1:completed,2:uncompleted');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callback_leads');
    }
};
