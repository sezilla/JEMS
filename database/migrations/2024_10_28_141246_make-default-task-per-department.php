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
        Schema::create('task_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('package_task_department', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID column
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('task_category_id');
        
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('task_category_id')->references('id')->on('task_category')->onDelete('cascade');
        
            $table->string('name');

            // Add unique constraint with a shorter name
            // $table->unique(['package_id', 'department_id', 'task_category_id'], 'pkg_dept_taskcat_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_task_department');
        Schema::dropIfExists('task_category');
    }
};
