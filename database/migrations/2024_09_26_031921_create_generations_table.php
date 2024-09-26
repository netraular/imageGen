<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenerationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id(); // Identificador único de la generación
            $table->text('sentence'); // Frase específica generada por el uso de valores específicos en una frase de la tabla `combinations`
            $table->foreignId('combination_id')->constrained('combinations'); // Relaciona la generación con una combinación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('generations');
    }
}