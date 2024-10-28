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
        Schema::create('package_task_department', function (Blueprint $table) {
            $table->id(); // Add an auto-incrementing ID column
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('department_id');
        
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        
            $table->string('name');
        
            $table->unique(['package_id', 'department_id']); // Use unique instead of primary
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_task_department');
    }
};
