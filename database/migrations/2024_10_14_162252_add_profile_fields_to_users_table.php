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
        Schema::table('users', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('llm_api_key')->nullable();
            $table->string('llm_service_name')->nullable();
            $table->string('comfyui_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'profile_photo',
                'llm_api_key',
                'llm_service_name',
                'comfyui_url',
            ]);
        });
    }
};