<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revoked_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('session_id')->constrained('auth_sessions')->cascadeOnDelete();
            $table->string('jti')->unique();
            $table->string('token_hash', 64);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revoked_access_tokens');
    }
};