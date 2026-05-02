<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_cargo', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('nm_cargo', 45);
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_cargo');
    }
};
