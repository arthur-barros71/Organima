<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_cena_roteiro', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('nm_cena_roteiro', 45);
            $table->string('ds_cena_roteiro', 250);
            $table->char('nm_cor', 7);
            $table->dateTime('dt_incial')->nullable();
            $table->dateTime('dt_final')->nullable();
            $table->longText('ds_texto')->nullable();

            $table->foreignId('id_roteiro')->nullable()->constrained('tb_roteiro')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_cena_roteiro');
    }
};
