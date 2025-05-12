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
        Schema::table('reservations', function (Blueprint $table) {
            // Si ya existe una columna 'notes', podemos renombrarla
            if (Schema::hasColumn('reservations', 'notes')) {
                $table->renameColumn('notes', 'special_requests');
            } else {
                // Si no existe, creamos la nueva columna
                $table->text('special_requests')->nullable()->after('duration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'special_requests')) {
                $table->renameColumn('special_requests', 'notes');
            } else {
                $table->dropColumn('special_requests');
            }
        });
    }
};
