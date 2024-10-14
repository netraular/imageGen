<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseLlmApiKeyLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Permitir valores nulos y aumentar el tamaño a 350 caracteres
            $table->string('llm_api_key', 350)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar la restricción de no nulos y el tamaño original si es necesario
            $table->string('llm_api_key', 191)->nullable(false)->change();
        });
    }
}