<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLlmResponsesPromptIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->dropForeign(['prompt_id']);
            $table->foreign('prompt_id')
                  ->references('id')
                  ->on('prompts')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->dropForeign(['prompt_id']);
            $table->foreign('prompt_id')
                  ->references('id')
                  ->on('prompts');
        });
    }
}