<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombinationsTable extends Migration
{
    public function up()
    {
        Schema::create('combinations', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->boolean('is_generated')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('combinations');
    }
}