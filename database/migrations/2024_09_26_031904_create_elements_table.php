<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('elements', function (Blueprint $table) {
            $table->id(); // Identificador único del elemento
            $table->foreignId('category_id')->constrained('categories'); // Relaciona el elemento con una categoría (obligatorio)
            $table->string('name'); // Nombre del elemento (obligatorio)
            $table->foreignId('parent_id')->nullable()->constrained('elements')->onDelete('cascade'); // Relaciona el elemento con otro elemento en la misma tabla (opcional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('elements');
    }
}