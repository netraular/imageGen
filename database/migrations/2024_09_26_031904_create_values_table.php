<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValuesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('values', function (Blueprint $table) {
            $table->id(); // Identificador único del valor
            $table->foreignId('category_id')->constrained('categories'); // Relaciona el valor con una categoría (obligatorio)
            $table->string('name'); // Nombre del valor (obligatorio)
            $table->foreignId('parent_id')->nullable()->constrained('values')->onDelete('cascade'); // Relaciona el valor con otro valor en la misma tabla (opcional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('values');
    }
}