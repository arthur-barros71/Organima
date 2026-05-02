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
        Schema::create('tb_erro', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('ds_erro', 500);
            $table->integer('nr_frame');
            $table->timestamp('dt_erro')->useCurrent();
            $table->timestamp('dt_conclusao')->nullable();
            $table->boolean('ic_conclusao');
            $table->string('nm_concluidor', 45)->nullable();

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
        Schema::dropIfExists('tb_erro');
    }
};
