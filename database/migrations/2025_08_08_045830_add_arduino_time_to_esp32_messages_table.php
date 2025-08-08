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
            $table->dateTime('arduino_time')->nullable()->after('ip_address');
            $table->string('led_state', 10)->default('off')->after('arduino_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esp32_messages', function (Blueprint $table) {
            $table->dropColumn('arduino_time');
            $table->dropColumn('led_state');
        });
    }
};
