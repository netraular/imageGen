<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateImagesLlmResponseIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['llm_response_id']);
            $table->foreign('llm_response_id')
                  ->references('id')
                  ->on('llm_responses')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['llm_response_id']);
            $table->foreign('llm_response_id')
                  ->references('id')
                  ->on('llm_responses');
        });
    }
}