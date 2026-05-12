<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supply extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 
        'code', 
        'name', 
        'unit_cost'
    ];

    /**
     * Relación con la empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}