<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auditoria_sgiem', function (Blueprint $table) {
            $table->id('auditoria_id');
            $table->foreignId('user_id')->constrained('users');
            $table->string('modelo', 50);
            $table->unsignedBigInteger('modelo_id');
            $table->enum('accion', ['crear', 'actualizar', 'eliminar']);
            $table->json('datos_previos')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamps();

            $table->index(['modelo', 'modelo_id']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditoria_sgiem');
    }
};
