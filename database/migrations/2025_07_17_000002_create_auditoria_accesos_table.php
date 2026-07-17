<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auditoria_accesos', function (Blueprint $table) {
            $table->id('acceso_id');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('accion', ['login', 'logout']);
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditoria_accesos');
    }
};
