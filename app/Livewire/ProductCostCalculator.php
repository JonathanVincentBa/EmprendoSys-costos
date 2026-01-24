<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Services\CostCalculatorService;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductCostCalculator extends Component
{
    public $selectedProductId = null;
    public $costResult = null;
    public $error = null;

    public function calculate()
    {
        $this->error = null;
        $this->costResult = null;

        if ($this->selectedProductId) {
            try {
                $calculator = new CostCalculatorService();
                $this->costResult = $calculator->calculateUnitCost((int) $this->selectedProductId);
            } catch (Exception $e) {
                $this->error = $e->getMessage();
            }
        }
    }

    public function render()
    {
        // Solo productos de la empresa del usuario logueado
        $products = Auth::check() && Auth::user()?->company_id
            ? Product::where('company_id', Auth::user()->company_id)->get()
            : collect();

        return view('livewire.product-cost-calculator', compact('products'));
    }
}