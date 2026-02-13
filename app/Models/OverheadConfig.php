<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class OverheadConfig extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'percentage', 'is_profit_margin'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
