<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoriesElementsForeignKeys extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('elements', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('elements', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories');
        });
    }
}