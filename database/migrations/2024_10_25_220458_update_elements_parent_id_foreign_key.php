<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateElementsParentIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('elements', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('elements')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('elements', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('elements');
        });
    }
}