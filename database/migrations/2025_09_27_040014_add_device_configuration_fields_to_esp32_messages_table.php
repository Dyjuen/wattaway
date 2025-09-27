<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('esp32_messages', function (Blueprint $table) {
            $table->string('endpoint')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('payload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esp32_messages', function (Blueprint $table) {
            $table->dropColumn(['endpoint', 'user_agent', 'payload']);
        });
    }
};
