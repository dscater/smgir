<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_solicitud', 255);
            $table->string('titulo', 255);
            $table->string('objetivo', 255);
            $table->string('dir', 255);
            $table->bigInteger('base_id')->unsigned();
            $table->bigInteger('macrodistrito_id')->unsigned();
            $table->bigInteger('distrito_id')->unsigned();
            $table->string('ubicacion_url', 255);
            $table->string('ubicacion_img', 255);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->integer('avance');
            $table->string('estado');
            $table->date('fecha_registro');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('base_id')->references('id')->on('bases')->ondelete('no action')->onupdate('cascade');
            $table->foreign('macrodistrito_id')->references('id')->on('macro_distritos')->ondelete('no action')->onupdate('cascade');
            $table->foreign('distrito_id')->references('id')->on('distritos')->ondelete('no action')->onupdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obras');
    }
}
