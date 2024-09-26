<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnDeleteCascadeToPromptsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->foreign('template_id')
                  ->references('id')
                  ->on('templates')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->foreign('template_id')
                  ->references('id')
                  ->on('templates');
        });
    }
}