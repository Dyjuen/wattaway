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
        Schema::table('esp32messagelogs', function (Blueprint $table) {
            $table->index('device_id');
            $table->index('created_at');
            $table->index(['device_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esp32messagelogs', function (Blueprint $table) {
            $table->dropIndex(['device_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['device_id', 'created_at']);
        });
    }
};
