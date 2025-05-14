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
        Schema::create('agreement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_agreement_id')->constrained('price_agreements')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->text('description');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('unit')->nullable(); // Ex: UN, M2, HR
            $table->enum('type', ['part', 'service']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_items');
    }
};
