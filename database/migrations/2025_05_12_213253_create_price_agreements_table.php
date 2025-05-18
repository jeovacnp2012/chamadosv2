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
        Schema::create('price_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->year('year');
            $table->date('signature_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('object');
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_agreements');
    }
};
