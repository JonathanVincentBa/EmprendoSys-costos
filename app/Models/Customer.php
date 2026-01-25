<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'identification', // Nuevo campo
        'email',
        'phone',
        'address',
        'type'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
