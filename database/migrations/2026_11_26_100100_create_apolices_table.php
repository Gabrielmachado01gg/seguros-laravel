<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apolices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('numero', 40)->unique();
            $table->string('ramo', 30);
            $table->string('seguradora', 80);
            $table->decimal('premio', 10, 2);
            $table->date('vigencia_inicio');
            $table->date('vigencia_fim');
            $table->string('status', 20)->default('ativa');
            $table->timestamps();

            $table->index(['status', 'vigencia_fim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apolices');
    }
};
