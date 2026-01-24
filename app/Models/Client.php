<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'company_id',
        'name',
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
