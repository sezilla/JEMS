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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('image')->nullable()->default('packages/package.png');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->string('image')->nullable()->default('departments/department.png');
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->string('image')->nullable()->default('teams/team.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
