<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointOfSale extends Component
{
    public $customerSearch = '';
    public $selectedCustomer = null;
    public $productSearch = '';
    public $selectedProduct = null;
    public $items = [];
    public $quantity = 1;
    public $unit_price = 0;

    public function selectCustomer($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $this->selectedCustomer = $customer->toArray();
            $this->customerSearch = '';
        }
    }

    public function selectProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $this->selectedProduct = $product;
            $this->unit_price = $product->price ?? 0; 
            $this->productSearch = $product->name;
        }
    }

    public function addItem()
    {
        if (!$this->selectedProduct) {
            $this->dispatch('swal', ['message' => 'Seleccione un producto', 'type' => 'warning']);
            return;
        }

        if ($this->quantity > $this->selectedProduct->current_stock) {
            $this->dispatch('swal', ['message' => 'Stock insuficiente', 'type' => 'error']);
            return;
        }

        $this->items[] = [
            'product_id' => $this->selectedProduct->id,
            'name' => $this->selectedProduct->name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->quantity * $this->unit_price,
        ];

        $this->reset(['selectedProduct', 'quantity', 'unit_price', 'productSearch']);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function saveInvoice()
    {
        if (!$this->selectedCustomer) {
            $this->dispatch('swal', ['message' => 'Falta el cliente', 'type' => 'warning']);
            return;
        }

        if (empty($this->items)) {
            $this->dispatch('swal', ['message' => 'Carrito vacío', 'type' => 'warning']);
            return;
        }

        try {
            DB::transaction(function () {
                $sale = Sale::create([
                    'company_id' => Auth::user()->company_id,
                    'customer_id' => $this->selectedCustomer['id'],
                    'sale_date' => now(),
                    'total' => collect($this->items)->sum('subtotal'),
                    'status' => 'completed'
                ]);

                foreach ($this->items as $item) {
                    SaleItem::create([
                        'company_id' => Auth::user()->company_id,
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price']
                    ]);
                    Product::find($item['product_id'])->decrement('current_stock', $item['quantity']);
                }
            });

            $this->reset(['items', 'selectedCustomer', 'customerSearch', 'productSearch']);
            $this->dispatch('swal', ['message' => '¡Venta guardada con éxito!', 'type' => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        $customers = [];
        if (strlen($this->customerSearch) > 1) {
            $customers = Customer::all()
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->customerSearch . '%')
                        ->orWhere('identification', 'like', '%' . $this->customerSearch . '%');
                })->limit(5)->get();
        }

        $products = [];
        if (strlen($this->productSearch) > 1 && (!$this->selectedProduct || $this->productSearch !== $this->selectedProduct->name)) {
            $products = Product::all()
                ->where('current_stock', '>', 0)
                ->where('name', 'like', '%' . $this->productSearch . '%')
                ->limit(5)->get();
        }

        return view('livewire.sales.point-of-sale', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }
}