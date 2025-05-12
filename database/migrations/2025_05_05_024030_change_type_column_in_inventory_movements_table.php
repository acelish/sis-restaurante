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
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Primero, elimina la columna original
            $table->dropColumn('type');
        });
        
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Luego, crea la nueva columna con los valores deseados
            $table->enum('type', ['entrada', 'salida', 'ajuste', 'purchase', 'usage', 'adjustment', 'waste'])
                  ->after('inventory_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->enum('type', ['purchase', 'usage', 'adjustment', 'waste'])
                  ->after('inventory_item_id');
        });
    }
};
