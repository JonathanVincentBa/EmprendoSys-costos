<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
     protected $fillable = ['company_id', 'code', 'name', 'unit_cost'];
    
    public function company() { return $this->belongsTo(Company::class); }
}
