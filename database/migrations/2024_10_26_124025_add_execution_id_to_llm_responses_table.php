<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExecutionIdToLlmResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->unsignedBigInteger('execution_id')->after('prompt_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->dropColumn('execution_id');
        });
    }
}