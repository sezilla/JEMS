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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        // Schema::create('team_leader', function (Blueprint $table) {
        //     $table->unsignedBigInteger('user_id');
        //     $table->unsignedBigInteger('team_id');
            
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            
        //     $table->primary(['user_id', 'team_id']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('team_leader');
        Schema::dropIfExists('teams');
    }
};
