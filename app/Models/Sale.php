<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use BelongsToCompany;

    protected $casts = [
        'sale_date' => 'datetime',
        'sri_authorization_date' => 'datetime',
    ];

    protected $fillable = [
        'company_id',
        'customer_id',
        'payment_method_sri',
        'user_id',
        'sale_date',
        'total',
        'subtotal_15',
        'subtotal_0',
        'iva_amount',
        'discount_amount',
        'status',
        'sri_access_key',
        'sri_status',
        'sri_response',
        'sri_authorization_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
