<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->unique(); // Nombre del servicio externo (API, programa, etc.)
            $table->boolean('is_paused')->default(false); // Estado de pausa del servicio
            $table->timestamp('resume_at')->nullable(); // Fecha y hora para reanudar el acceso
            $table->string('pause_reason')->nullable(); // Motivo de la pausa
            $table->unsignedInteger('retry_count')->default(0); // Contador de reintentos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_party_services');
    }
}
