<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        // 1. Obtener datos necesarios de la empresa
        $vendedores = User::role('vendedor')->where('company_id', $companyId)->get();
        $clientes = Customer::where('company_id', $companyId)->get();
        $productos = Product::where('company_id', $companyId)->where('is_active', true)->get();

        if ($vendedores->isEmpty() || $productos->isEmpty() || $clientes->isEmpty()) {
            return;
        }

        // 2. Generar ventas para los últimos 3 días (incluyendo hoy)
        // Para cumplir con "25 ventas diarias por empresa"
        for ($diasAtras = 0; $diasAtras < 3; $diasAtras++) {
            $fecha = Carbon::now()->subDays($diasAtras);

            for ($i = 1; $i <= 25; $i++) {
                $vendedor = $vendedores->random();
                $cliente = $clientes->random();
                $producto = $productos->random();

                // Generar un total aleatorio (puedes ajustarlo según tus precios)
                $total = rand(10, 150) + (rand(0, 99) / 100);

                Sale::create([
                    'company_id' => $companyId,
                    'user_id'    => $vendedor->id,
                    'customer_id' => $cliente->id,
                    'sale_date'  => $fecha->copy()->setHour(rand(8, 18))->setMinute(rand(0, 59)), // Campo requerido por tu migración
                    'total'      => rand(10, 150) + (rand(0, 99) / 100),
                    'status'     => 'completed',
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
            }
        }
    }
}
