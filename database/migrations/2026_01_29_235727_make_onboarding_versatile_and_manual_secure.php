<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('onboarding_seen_version')->default(0)->after('has_seen_welcome');
        });

        Schema::table('manual_pages', function (Blueprint $table) {
            $table->string('required_role')->nullable()->after('slug')->comment('If null, visible to all. If set, only visible to users with this role.');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_seen_version');
        });

        Schema::table('manual_pages', function (Blueprint $table) {
            $table->dropColumn('required_role');
        });
    }
};
