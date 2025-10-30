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
    Schema::create('equipment', function (Blueprint $table) {
        $table->id();

        // Identificadores del negocio
        $table->string('code', 50)->unique();   // Código interno único, p.ej. EQ-0041
        $table->string('name', 150);            // Nombre comercial (Andamio 2m, Revolvedora, etc.)
        $table->string('category', 100)->nullable(); // Categoría (opcional): andamios, compactación, corte, etc.

        // Estado operacional del equipo
        $table->enum('status', ['available','rented','workshop'])
              ->default('available')
              ->index(); // índice para filtrar rápido por estado

        // Ubicación del equipo: en almacén o en obra (con dirección)
        $table->enum('location_type', ['warehouse','site'])
              ->default('warehouse')
              ->index();

        $table->string('site_address')->nullable(); // si está en obra, dónde

        // Economía
        $table->decimal('daily_rate', 10, 2)->default(0); // tarifa diaria MXN
        $table->text('notes')->nullable();                // observaciones

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
