<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuadro_v2', function (Blueprint $table) {
            $table->string('pivot_label', 100)->nullable()->default('PIVOTE');
        });
    }

    public function down(): void
    {
        Schema::table('cuadro_v2', function (Blueprint $table) {
            $table->dropColumn('pivot_label');
        });
    }
};
