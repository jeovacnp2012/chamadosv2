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
        Schema::create('calleds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sector_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patrimony_id')->constrained()->cascadeOnDelete();

            $table->text('problem');
            $table->string('protocol', 30)->unique();

            $table->enum('status', ['A', 'F'])->default('A')->comment('A = Aberto, F = Fechado');
            $table->enum('type_maintenance', ['P', 'C'])->comment('P = Preventiva, C = Corretiva');

            $table->date('closing_date')->nullable();
            $table->boolean('patrimony')->nullable(); // indicar se é patrimonial ou não (?)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calleds');
    }
};
