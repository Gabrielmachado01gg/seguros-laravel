<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apolice_id')->constrained('apolices')->cascadeOnDelete();
            $table->string('canal', 20);
            $table->string('status', 20);
            $table->text('erro')->nullable();
            $table->timestamps();

            $table->index(['apolice_id', 'canal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
