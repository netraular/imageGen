<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLlmResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('llm_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combination_id')
                  ->constrained()
                  ->onDelete('cascade'); // Añadir esta línea
            $table->text('response');
            $table->string('source'); // e.g., "Groq API"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('llm_responses');
    }
}