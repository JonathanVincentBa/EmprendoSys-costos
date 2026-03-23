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
        // CORRECCIÓN: Usamos Customer::query() para no traer todo a memoria
        if (strlen($this->customerSearch) > 1) {
            $customers = Customer::query()
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->customerSearch . '%')
                        ->orWhere('identification', 'like', '%' . $this->customerSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        $products = [];
        // CORRECCIÓN: Quitamos el Product::all() que rompe el limit()
        if (strlen($this->productSearch) > 1 && (!$this->selectedProduct || $this->productSearch !== $this->selectedProduct->name)) {
            $products = Product::query()
                ->where('current_stock', '>', 0)
                ->where('name', 'like', '%' . $this->productSearch . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.sales.point-of-sale', [
            'customers' => $customers,
            'products' => $products,
            'total' => collect($this->items)->sum(fn($item) => $item['quantity'] * $item['unit_price'])
        ]);
    }

    public function store()
    {
        // 1. Verificaciones de seguridad y datos
        if (!$this->selectedCustomer) {
            $this->dispatch('swal', ['message' => 'Debe seleccionar un cliente', 'type' => 'error']);
            return;
        }

        if (empty($this->items)) {
            $this->dispatch('swal', ['message' => 'El carrito está vacío', 'type' => 'warning']);
            return;
        }

        try {
            DB::beginTransaction();

            // 2. Crear la cabecera de la venta
            $sale = Sale::create([
                'company_id'  => Auth::user()->company_id,
                'customer_id' => $this->selectedCustomer['id'],
                'user_id'     => Auth::id(),
                'total'       => collect($this->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']),
                'status'      => 'completed',
                'date'        => now(),
            ]);

            // 3. Registrar cada producto y descontar stock
            foreach ($this->items as $item) {
                // Guardar detalle
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['quantity'] * $item['unit_price'],
                ]);

                // Descontar Stock del producto
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('current_stock', $item['quantity']);
                }
            }

            DB::commit();

            // 4. Limpiar el formulario y avisar al usuario
            $this->reset(['items', 'selectedCustomer', 'customerSearch', 'productSearch', 'selectedProduct']);

            $this->dispatch('swal', [
                'message' => '¡Venta y Factura generada con éxito!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'message' => 'Error al procesar la venta: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}
