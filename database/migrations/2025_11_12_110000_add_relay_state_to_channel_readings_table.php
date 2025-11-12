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
        Schema::table('channel_readings', function (Blueprint $table) {
            $table->string('relay_state', 8)->after('power')->default('off');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channel_readings', function (Blueprint $table) {
            $table->dropColumn('relay_state');
        });
    }
};
