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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre comercial
            $table->string('razon_social')->nullable(); // Razón social o nombres completos
            $table->string('ruc', 13)->unique();
            $table->string('address')->nullable(); // Dirección matriz
            $table->string('establishment_address')->nullable(); // Dirección del establecimiento
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('active');
            $table->string('logo')->nullable();

            // Configuración Tributaria SRI
            $table->string('estab', 3)->default('001'); // Establecimiento ej: 001
            $table->string('pto_emi', 3)->default('001'); // Punto de emisión ej: 001
            $table->string('contribuyente_especial', 10)->nullable();
            $table->enum('obligado_contabilidad', ['SI', 'NO'])->default('NO');
            $table->string('contribuyente_rimpe')->nullable(); // Ej: CONTRIBUYENTE RÉGIMEN RIMPE
            
            // Firma Electrónica (.p12)
            $table->string('signature_path')->nullable();
            $table->text('signature_password')->nullable();
            
            // Ambiente SRI (1: Pruebas, 2: Producción)
            $table->enum('sri_environment', ['1', '2'])->default('1');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
