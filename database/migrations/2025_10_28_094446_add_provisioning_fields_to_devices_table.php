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
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('provisioning_token_id')->nullable()->after('id')->constrained('device_provisioning_tokens')->onDelete('set null');
            $table->string('serial_number', 64)->unique()->after('provisioning_token_id');
            $table->string('hardware_id', 64)->unique()->after('serial_number');
            $table->timestamp('activated_at')->nullable()->after('hardware_id');

            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['provisioning_token_id']);
            $table->dropColumn('provisioning_token_id');
            $table->dropColumn('serial_number');
            $table->dropColumn('hardware_id');
            $table->dropColumn('activated_at');
        });
    }
};
