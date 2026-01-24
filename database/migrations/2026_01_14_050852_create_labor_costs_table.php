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
        Schema::create('labor_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('role');
            $table->decimal('monthly_salary', 10, 2);
            $table->decimal('iess_rate', 5, 2)->default(0.00); // %
            $table->decimal('decimo_tercero_rate', 5, 2)->default(0.00);
            $table->decimal('decimo_cuarto_rate', 5, 2)->default(0.00);
            $table->decimal('vacation_rate', 5, 2)->default(0.00);
            $table->decimal('fondo_reserva_rate', 5, 2)->default(0.00);
            $table->decimal('severance_rate', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_costs');
    }
};
