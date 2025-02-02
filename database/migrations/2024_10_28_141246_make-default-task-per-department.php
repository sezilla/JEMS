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

        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID column
            // $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('task_category_id');
        
            // $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('task_category_id')->references('id')->on('task_category')->onDelete('cascade');
        
            $table->string('name');
            // $table->text('trello_card_id')->nullable();
            // $table->text('trello_checklist_id')->nullable();
            $table->text('description')->nullable();

            // Add unique constraint with a shorter name
            // $table->unique(['package_id', 'department_id', 'task_category_id'], 'pkg_dept_taskcat_unique');
        });

        Schema::create('trello_package_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('package_id');
            $table->string('trello_card_id');
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });

        Schema::create('task_package', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('package_id');
            
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

            $table->string('trello_checklist_item_id')->nullable();
            
        });

        // Schema::table('trello_task_package', function (Blueprint $table) {
        //     $table->unsignedBigInteger('task_package_id');

        //     $table->foreign('task_id')->references('id')->on('task_package')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_package');
        Schema::dropIfExists('trello_package_cards');
        Schema::dropIfExists('task_category');
    }
};
