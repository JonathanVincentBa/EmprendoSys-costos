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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_method_sri', 2)->default('01')->after('customer_id'); // 01: Efectivo/Sin Sist. Fin.
            $table->decimal('subtotal_15', 12, 2)->default(0)->after('total');
            $table->decimal('subtotal_0', 12, 2)->default(0)->after('subtotal_15');
            $table->decimal('iva_amount', 12, 2)->default(0)->after('subtotal_0');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('iva_amount');
            $table->string('sri_access_key', 49)->nullable()->after('status');
            $table->string('sri_authorization_date')->nullable()->after('sri_access_key');
        });
        
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('discount', 12, 2)->default(0)->after('unit_price');
            $table->string('vat_code', 5)->default('4')->after('discount'); // 4: 15% (según catálogo SRI)
            $table->decimal('vat_rate', 5, 2)->default(15.00)->after('vat_code');
            $table->decimal('vat_amount', 12, 2)->default(0)->after('vat_rate');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_method_sri', 'subtotal_15', 'subtotal_0', 'iva_amount', 'discount_amount', 'sri_access_key', 'sri_authorization_date']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['discount', 'vat_code', 'vat_rate', 'vat_amount']);
        });
    }
};
