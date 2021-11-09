<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMantenimientoReportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mantenimiento_reportes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nro')->unsigned();
            $table->bigInteger('mantenimiento_id')->unsigned();
            $table->text('trabajo_realizado');
            $table->string('grupo_trabajo', 255);
            $table->text('maquinaria');
            $table->string('fotografia', 255);
            $table->integer('avance');
            $table->string('estado');
            $table->date('fecha_fin')->nullable();
            $table->string('observaciones', 255);
            $table->date('fecha_registro');
            $table->bigInteger('registro_id')->unsigned();
            $table->timestamps();

            $table->foreign('mantenimiento_id')->references('id')->on('mantenimientos')->ondelete('no action')->onupdate('cascade');
            $table->foreign('registro_id')->references('id')->on('users')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mantenimiento_reportes');
    }
}
