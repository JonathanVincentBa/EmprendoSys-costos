<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyUsage extends Model
{
    protected $fillable = ['company_id', 'recipe_id', 'supply_id', 'quantity'];
    
    public function company() { return $this->belongsTo(Company::class); }
    public function supply() { return $this->belongsTo(Supply::class); }
}
