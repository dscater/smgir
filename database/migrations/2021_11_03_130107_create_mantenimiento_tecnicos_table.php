<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMantenimientoTecnicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mantenimiento_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reporte_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->ondelete('no action')->onupdate('cascade');
            $table->foreign('reporte_id')->references('id')->on('mantenimiento_reportes')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mantenimiento_tecnicos');
    }
}
