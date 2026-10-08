<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); 
            
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->string('model_id', 36)->nullable(); 
            $table->jsonb('metadata')->nullable(); 
            $table->ipAddress('ip_address')->nullable();
            
            $table->timestamps();

            $table->index(['model_type', 'model_id']); 
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};