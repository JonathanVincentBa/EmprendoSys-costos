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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 4)->default(0)->after('packaging_type')->comment('Costo de producción unitario');
            $table->decimal('price', 10, 4)->default(0)->after('unit_cost')->comment('Precio de venta final sugerido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit_cost', 'price']);
        });
    }
};
