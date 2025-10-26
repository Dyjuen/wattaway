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
        Schema::create('esp32messagelogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->text('content');
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('endpoint', 255)->nullable();
            $table->text('payload')->nullable();
            $table->float('voltage')->nullable();
            $table->float('current')->nullable();
            $table->float('power')->nullable();
            $table->float('energy')->nullable();
            $table->float('frequency')->nullable();
            $table->float('power_factor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esp32messagelogs');
    }
};
