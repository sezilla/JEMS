<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Namu\WireChat\Enums\ConversationType;

class UpdateConversationsTypeColumn extends Migration
{
    public function up()
    {
        Schema::table('wire_conversations', function (Blueprint $table) {
            // Modify the type column to have a default value
            $table->string('type')
                ->default(ConversationType::GROUP->value)
                ->change();
        });
    }

    public function down()
    {
        Schema::table('wire_conversations', function (Blueprint $table) {
            // Revert to nullable if needed
            $table->string('type')
                ->nullable()
                ->change();
        });
    }
}
