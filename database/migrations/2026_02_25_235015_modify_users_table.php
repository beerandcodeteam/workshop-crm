<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete()->after('id');
            $table->foreignId('role_id')->constrained()->restrictOnDelete()->after('tenant_id');
            $table->foreignId('user_status_id')->constrained()->restrictOnDelete()->after('role_id');

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['role_id']);
            $table->dropForeign(['user_status_id']);
            $table->dropColumn(['tenant_id', 'role_id', 'user_status_id']);
        });
    }
};
