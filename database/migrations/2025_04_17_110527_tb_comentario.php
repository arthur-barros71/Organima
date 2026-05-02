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
        Schema::create('tb_comentario', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('ds_comentario', 500);
            $table->timestamp('dt_comentario')->useCurrent();

            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('id_roteiro')->nullable()->constrained('tb_roteiro')->onDelete('cascade');
            $table->foreignId('id_projeto')->nullable()->constrained('tb_projeto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_comentario');
    }
};
