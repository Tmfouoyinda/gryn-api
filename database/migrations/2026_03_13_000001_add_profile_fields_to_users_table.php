<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->unsignedInteger('points')->default(0)->after('last_name');
            $table->unsignedTinyInteger('level')->default(1)->after('points');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) =>
            $t->dropColumn(['first_name', 'last_name', 'points', 'level'])
        );
    }
};
