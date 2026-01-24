<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $fillable = ['company_id', 'name', 'presentation_ml', 'packaging_type', 'is_active'];
    
    public function company() { return $this->belongsTo(Company::class); }
    public function recipes() { return $this->hasMany(Recipe::class); }
}
