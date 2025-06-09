<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('id');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });

        // Migrate data from users_has_teams to users.team_id
        // If a user has multiple teams, only the first one (by team_id) will be set
        DB::statement('
            UPDATE users u
            INNER JOIN (
                SELECT user_id, MIN(team_id) as team_id
                FROM users_has_teams
                GROUP BY user_id
            ) ut ON u.id = ut.user_id
            SET u.team_id = ut.team_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
