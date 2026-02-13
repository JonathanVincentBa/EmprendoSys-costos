<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use BelongsToCompany;
    
    protected $fillable = ['company_id', 'code', 'name', 'unit_cost', 'unit'];
    
    public function company() { return $this->belongsTo(Company::class); }
}
