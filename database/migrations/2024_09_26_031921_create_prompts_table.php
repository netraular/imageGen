<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromptsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id(); // Identificador único del prompt
            $table->text('sentence'); // Frase específica generada por el uso de elementos específicos en una frase de la tabla `templates`
            $table->foreignId('template_id')->constrained('templates'); // Relaciona el prompt con una plantilla
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('prompts');
    }
}