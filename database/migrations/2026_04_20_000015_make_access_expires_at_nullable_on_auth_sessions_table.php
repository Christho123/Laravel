<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE auth_sessions MODIFY access_expires_at TIMESTAMP NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE auth_sessions SET access_expires_at = CURRENT_TIMESTAMP WHERE access_expires_at IS NULL');
        DB::statement('ALTER TABLE auth_sessions MODIFY access_expires_at TIMESTAMP NOT NULL');
    }
};
