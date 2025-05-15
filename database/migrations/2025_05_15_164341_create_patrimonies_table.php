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
        Schema::create('patrimonies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            $table->string('tag')->unique(); // plaqueta de patrimônio
            $table->string('description')->nullable(); // descrição do equipamento
            $table->string('observation')->nullable();
            $table->string('image_path')->nullable();

            $table->dateTime('purchase_date')->nullable();
            $table->float('purchase_value')->nullable();

            $table->string('write_off_reason', 80)->nullable();
            $table->dateTime('write_off_date')->nullable();
            $table->boolean('has_report')->nullable();
            $table->dateTime('report_date')->nullable();

            $table->string('type', 80)->nullable(); // tipo do bem
            $table->string('acquisition_type', 80)->nullable();
            $table->float('acquisition_value')->nullable();
            $table->timestamp('acquisition_date')->nullable();
            $table->float('current_value')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimonies');
    }
};
