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
        Schema::create('tb_projeto', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('nm_projeto', 45);
            $table->string('ds_projeto', 500);
            $table->timestamp('dt_inicial');
            $table->timestamp('dt_conclusao')->nullable();
            $table->integer('qt_frames')->default(0);
            $table->integer('ds_fps')->default(24);
            $table->decimal('qt_volume', 3, 2)->default(1);
            $table->string('nm_proporcao', 45)->default('16:9');
            $table->timestamps();
        
            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('id_roteiro')->nullable()->constrained('tb_roteiro')->onDelete('cascade');
            $table->foreignId('id_tipo')->constrained('tb_tipo')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_projeto');
    }
};