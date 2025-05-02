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
            $table->string('task_name')->nullable()->after('check_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropColumn('task_name');
        });
    }
};
