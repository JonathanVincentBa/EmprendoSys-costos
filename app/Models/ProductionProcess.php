<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductionProcess extends Model
{
    use BelongsToCompany;
    
    protected $fillable = ['company_id', 'name', 'hours_per_batch'];
    
    public function company() { return $this->belongsTo(Company::class); }
}