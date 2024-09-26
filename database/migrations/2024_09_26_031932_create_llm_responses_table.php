<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLlmResponsesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('llm_responses', function (Blueprint $table) {
            $table->id(); // Identificador único de la respuesta
            $table->foreignId('prompt_id')->constrained('prompts'); // Relaciona la respuesta con un prompt
            $table->text('response'); // El texto generado por IA
            $table->string('source'); // Modelo de IA que se ha usado para generar la respuesta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('llm_responses');
    }
}