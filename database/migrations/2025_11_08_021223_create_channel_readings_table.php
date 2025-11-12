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
        Schema::create('channel_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_reading_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('channel');
            $table->float('current');
            $table->integer('current_raw');
            $table->float('power');
            $table->timestamps();

            $table->index(['device_reading_id', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_readings');
    }
};
