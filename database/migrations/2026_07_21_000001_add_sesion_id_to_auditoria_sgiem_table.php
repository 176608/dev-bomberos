<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditoria_sgiem', function (Blueprint $table) {
            $table->string('sesion_id', 36)->nullable()->after('datos_nuevos');
            $table->index('sesion_id');
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_sgiem', function (Blueprint $table) {
            $table->dropIndex(['sesion_id']);
            $table->dropColumn('sesion_id');
        });
    }
};
