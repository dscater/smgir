<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObraTecnicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obra_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reporte_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('reporte_id')->references('id')->on('obra_reportes')->ondelete('no action')->onupdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obra_tecnicos');
    }
}
