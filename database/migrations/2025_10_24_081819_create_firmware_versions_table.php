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
        Schema::create('firmware_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->text('description');
            $table->string('file_path');
            $table->integer('file_size');
            $table->string('checksum');
            $table->boolean('is_stable')->default(false);
            $table->string('required_version')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index('is_stable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firmware_versions');
    }
};
