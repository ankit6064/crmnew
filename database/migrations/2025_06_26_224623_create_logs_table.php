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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('type')->nullable()->comment('1:notes,2:leadstatuschanged,3:lhscreated,4:momcreated,5:leadaddedforapproval,6:lhsupdated,7:addemployee,8:managelogin,9:active/deactive,10:assignedsubmanager,11:deleteemployee,12:leadstransfered,13:campaignactive/deactive,14:newcampaignadded,15:leadapproved/disapproved,16:employeeassigned');
            $table->integer('reference_id')->nullable();
            $table->integer('note_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('source_id')->nullable();
            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
