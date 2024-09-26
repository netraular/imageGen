<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id(); // Identificador único de la imagen
            $table->foreignId('llm_response_id')->constrained('llm_responses'); // Relaciona la imagen generada con la respuesta
            $table->string('image_path'); // Indica el path del archivo imagen que se ha generado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}