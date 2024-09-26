<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToLlmResponsesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}