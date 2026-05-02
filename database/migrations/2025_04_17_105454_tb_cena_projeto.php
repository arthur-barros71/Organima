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
        Schema::create('tb_cena_projeto', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('nm_cena_projeto', 45);
            $table->string('ds_cena_projeto', 250);
            $table->integer('nr_frame_inicial');
            $table->integer('nr_frame_final');
            $table->integer('qt_frame')->nullable();
            $table->char('nm_cor', 7);
            $table->boolean('ic_conclusao')->default(0);

            $table->foreignId('id_cena_roteiro')->nullable()->constrained('tb_roteiro')->onDelete('cascade');
            $table->foreignId('id_projeto')->nullable()->constrained('tb_projeto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_cena_projeto');
    }
};
