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
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->after('id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->date('due_date')->after('card_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('project_id');
        });
    }
};
