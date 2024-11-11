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
        Schema::table('task_category', function (Blueprint $table) {
            $table->decimal('start_percentage', 5, 2)->nullable()->comment('Starting percentage of the range');
            $table->decimal('max_percentage', 5, 2)->nullable()->comment('Maximum percentage of the range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_category', function (Blueprint $table) {
            $table->dropColumn('start_percentage');
            $table->dropColumn('max_percentage');
        });
    }
};
