<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullResponseInLlmResponsesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->text('response')->nullable()->change();
            $table->string('source')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('llm_responses', function (Blueprint $table) {
            $table->text('response')->nullable(false)->change();
            $table->string('source')->nullable(false)->change();
        });
    }
}