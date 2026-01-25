<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $identification, $email, $phone, $address, $customer_id;
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|min:3',
        'identification' => 'required',
        'email' => 'nullable|email',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::where('company_id', Auth::user()->company_id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('identification', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.sales.customers', [
            'customers' => $customers
        ]);
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
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
            'message' => $this->customer_id ? 'Cliente actualizado' : 'Cliente guardado',
            'type' => 'success'
        ]);

        $this->closeModal();
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

    public function delete($id)
    {
        Customer::find($id)->delete();
        $this->dispatch('swal', ['message' => 'Cliente eliminado', 'type' => 'warning']);
    }
}