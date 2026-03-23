<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Customers extends Component
{
    use WithPagination, AuthorizesRequests;

    // Propiedades de búsqueda e interfaz
    public $search = '';
    public $isModalOpen = false;

    // Propiedades del formulario (Inicializadas para evitar errores de tipado)
    public $customer_id = null;
    public $name = '';
    public $identification = '';
    public $email = '';
    public $phone = '';
    public $address = '';

    protected $rules = [
        'name' => 'required|min:3',
        'identification' => 'required',
        'email' => 'nullable|email',
    ];

    // Resetear página al buscar para evitar que la tabla quede vacía en páginas altas
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        // Verificar permiso antes de abrir el modal
        $this->authorize('crear catalogos'); 
        
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        // Verificar permiso
        $this->authorize('editar catalogos');

        $customer = Customer::findOrFail($id);
        
        $this->customer_id = $id;
        $this->name = $customer->name;
        $this->identification = $customer->identification;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        // Seguridad: El vendedor no puede guardar si no tiene el permiso
        $this->authorize($this->customer_id ? 'editar catalogos' : 'crear catalogos');

        $this->validate();

        Customer::updateOrCreate(['id' => $this->customer_id], [
            'company_id' => Auth::user()->company_id,
            'name' => $this->name,
            'identification' => $this->identification,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->dispatch('swal', [
            'message' => $this->customer_id ? 'Cliente actualizado correctamente' : 'Cliente guardado con éxito',
            'type' => 'success'
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        // Seguridad: El vendedor no puede borrar
        $this->authorize('eliminar catalogos');

        Customer::findOrFail($id)->delete();

        $this->dispatch('swal', [
            'message' => 'Cliente eliminado',
            'type' => 'info'
        ]);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->reset(['name', 'identification', 'email', 'phone', 'address', 'customer_id']);
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Iniciamos el Query Builder
        $query = Customer::query();

        // 2. Filtro Multi-tenant (Seguridad de datos por empresa)
        if ($user && !$user->hasRole('super-admin')) {
            $query->where('company_id', $user->company_id);
        }

        // 3. Filtro de búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('identification', 'like', '%' . $this->search . '%');
            });
        }

        // 4. Retornamos la vista con la variable 'customers' paginada
        return view('livewire.sales.customers', [
            'customers' => $query->latest()->paginate(10)
        ]);
    }
}