<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('calleds', function (Blueprint $table) {
            // Remover a coluna patrimony (se existir)
            if (Schema::hasColumn('calleds', 'patrimony')) {
                $table->dropColumn('patrimony');
            }

            // Adicionar a nova coluna called_type_id
            $table->foreignId('called_type_id')
                ->after('id')
                ->constrained('called_types')
                ->onDelete('cascade');

            // Tornar patrimony_id nullable (já que serviços gerais não terão patrimônio)
            $table->unsignedBigInteger('patrimony_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('calleds', function (Blueprint $table) {
            $table->dropForeign(['called_type_id']);
            $table->dropColumn('called_type_id');

            // Reverter para não nullable se necessário
            $table->unsignedBigInteger('patrimony_id')->nullable(false)->change();

            // Recriar a coluna patrimony se necessário
            $table->boolean('patrimony')->default(true);
        });
    }
};
