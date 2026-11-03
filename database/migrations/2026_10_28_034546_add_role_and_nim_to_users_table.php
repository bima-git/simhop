<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
        public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'coach', 'mahasiswa'])
                ->default('mahasiswa')
                ->after('password');
            $table->string('nim', 20)->nullable()->unique()->after('role');
            $table->boolean('is_active')->default(true)->after('nim');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nim']);
            $table->dropColumn(['role', 'nim', 'is_active']);
        });
    }
};
