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
        Schema::create('task_due_update_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_task_id');
            $table->unsignedBigInteger('user_id');

            $table->date('old_due_date');
            $table->date('new_due_date');
            $table->text('remarks')->nullable();
            
            $table->foreign('user_task_id')->references('id')->on('user_tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_due_update_histories');
    }
};
