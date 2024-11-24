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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');   
            $table->string('title')->nullable();
            // $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->nullable();  
            $table->string('body',5000)->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();;
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });

        // Schema::create('reactions', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('cascade');
        //     $table->foreignId('comment_id')->nullable()->constrained('comments')->onDelete('cascade');
        //     $table->string('type'); // e.g., 'like', 'love', etc.
        //     $table->timestamps();
        // });

        Schema::create('replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('comment_id')->constrained('comments')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('comments');
        // Schema::dropIfExists('reactions');
        Schema::dropIfExists('replies');
    }
};
