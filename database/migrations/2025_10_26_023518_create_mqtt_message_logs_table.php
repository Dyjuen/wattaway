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
        Schema::create('mqtt_message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('devices')->onDelete('cascade');
            $table->enum('direction', ['incoming', 'outgoing']); // incoming from device, outgoing to device
            $table->enum('type', ['data', 'command', 'status', 'error']); // message type
            $table->string('topic')->nullable(); // MQTT topic
            $table->text('endpoint')->nullable(); // HTTP endpoint (for REST API calls)
            $table->longText('payload'); // JSON message content
            $table->string('status')->default('success'); // success, error, pending
            $table->text('error_message')->nullable(); // if status is error
            $table->string('ip_address')->nullable();
            $table->integer('response_code')->nullable(); // HTTP response code
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for fast querying
            $table->index('device_id');
            $table->index('direction');
            $table->index('type');
            $table->index('created_at');
            $table->index(['device_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mqtt_message_logs');
    }
};
