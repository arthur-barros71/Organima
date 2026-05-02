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
        Schema::create('tb_usuario', function (Blueprint $table) { // Alterei ~Nicolas
            $table->id();
            $table->string('nm_usuario', 45);
            $table->string('ds_email', 45)->unique();
            $table->string('ds_email_recuperacao', 45)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('cd_senha');
            $table->rememberToken();
            $table->string('nr_telefone')->nullable();
            $table->integer('qt_projeto')->default(0);
            $table->integer('qt_roteiro')->default(0);
            $table->boolean('ic_suspensao')->default(false);
            $table->dateTime('dt_suspensao')->nullable();
            $table->string('im_usuario')->nullable()->default(null);
            $table->timestamps();
        });        

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_usuario');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
