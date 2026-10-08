<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('legal_name');
            $table->string('contact_email');
            $table->string('tax_id', 50)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('country_code', 2);
            
            $table->timestamps();

            $table->index('legal_name');
            $table->index('tax_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};