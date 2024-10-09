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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trello_board_id')->nullable();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // project is created by
            $table->date('event_date');
            $table->string('venue')->nullable();

            $table->string('groom_name');
            $table->string('bride_name');
            $table->string('theme_color')->nullable();
            $table->text('special_request')->nullable();
            $table->string('thumbnail_path')->nullable();

            $table->foreignId('groom_coordinator')->constrained('users')->onDelete('cascade');
            $table->foreignId('bride_coordinator')->constrained('users')->onDelete('cascade');
            $table->foreignId('head_coordinator')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });




        Schema::create('project_coordinators', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->primary(['user_id', 'project_id']); // Composite primary key
        });

        Schema::create('project_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('team_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->primary(['project_id', 'team_id']); // Composite primary key
        });

        // Schema::create('project_package', function (Blueprint $table) {
        //     $table->unsignedBigInteger('project_id');
        //     $table->unsignedBigInteger('package_id');

        //     $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        //     $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

        //     $table->primary(['project_id', 'package_id']); // Composite primary key
        // });

        // Schema::create('project_attribute_value', function (Blueprint $table) {
        //     $table->unsignedBigInteger('attribute_id');
        //     $table->unsignedBigInteger('project_id');

        //     $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        //     $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        
        //     $table->string('value');
        //     $table->timestamps();
        
        //     // Ensure the column is unique
        //     $table->primary(['attribute_id', 'project_id']); // Use unique constraint
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('project_attribute_value');
        // Schema::dropIfExists('project_package');
        Schema::dropIfExists('project_teams');
        Schema::dropIfExists('project_coordinators');
        Schema::dropIfExists('projects');
    }
};
