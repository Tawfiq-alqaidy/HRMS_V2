<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            if (!Schema::hasColumn('password_resets', 'failed_attempts')) {
                $table->unsignedTinyInteger('failed_attempts')->default(0);
            }
            // Index already exists on email, so no need to add again
        });
    }

    public function down(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            if (Schema::hasColumn('password_resets', 'failed_attempts')) {
                $table->dropColumn('failed_attempts');
            }
        });
    }
};
