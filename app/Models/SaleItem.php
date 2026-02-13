<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use BelongsToCompany;   
    
    protected $fillable = ['company_id', 'sale_id', 'product_id', 'quantity', 'unit_price'];
    
    public function company() { return $this->belongsTo(Company::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
