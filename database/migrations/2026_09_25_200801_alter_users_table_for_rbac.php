<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            
            
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['role_id', 'company_id', 'is_active', 'last_login_at', 'deleted_at']);
        });
    }
};