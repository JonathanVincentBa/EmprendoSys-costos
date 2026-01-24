<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverheadConfig extends Model
{
    protected $fillable = ['company_id', 'name', 'percentage', 'is_profit_margin'];
    
    public function company() { return $this->belongsTo(Company::class); }
}
