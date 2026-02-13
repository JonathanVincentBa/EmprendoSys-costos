<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToCompany;

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
