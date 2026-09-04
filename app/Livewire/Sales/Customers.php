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

    public $search = '';
    public $isModalOpen = false;

    // Propiedades del formulario
    public $customer_id = null;
    public $name = '';
    public $identification_type = '05'; // Valor por defecto: Cédula
    public $identification = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $type = 'minorista'; // Valor por defecto para el ENUM

    protected $rules = [
        'name' => 'required|min:3',
        'identification_type' => 'required',
        'identification' => 'required',
        'email' => 'nullable|email',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('gestionar clientes');
        
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->authorize('gestionar clientes');

        $customer = Customer::findOrFail($id);
        
        $this->customer_id = $id;
        $this->name = $customer->name;
        $this->identification_type = $customer->identification_type ?? '05';
        $this->identification = $customer->identification;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->type = $customer->type ?? 'minorista';
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->authorize('gestionar clientes');

        $this->validate();

        Customer::updateOrCreate(['id' => $this->customer_id], [
            'company_id' => Auth::user()->company_id,
            'name' => $this->name,
            'identification_type' => $this->identification_type,
            'identification' => $this->identification,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'type' => $this->type,
        ]);

        $this->dispatch('swal', [
            'message' => $this->customer_id ? 'Cliente actualizado correctamente' : 'Cliente guardado con éxito',
            'type' => 'success'
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        $this->authorize('gestionar clientes');

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
        $this->reset(['name', 'identification_type', 'identification', 'email', 'phone', 'address', 'customer_id', 'type']);
        $this->identification_type = '05';
        $this->type = 'minorista';
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Customer::query();

        if ($user && !$user->hasRole('super-admin')) {
            $query->where('company_id', $user->company_id);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('identification', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.sales.customers', [
            'customers' => $query->latest()->paginate(10)
        ]);
    }
}