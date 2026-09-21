<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('team_members')) {
            return;
        }

        if (Schema::hasColumn('team_members', 'group')) {
            return;
        }

        Schema::table('team_members', function (Blueprint $table) {
            $table->string('group')->default('team')->after('role');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('team_members') || ! Schema::hasColumn('team_members', 'group')) {
            return;
        }

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
