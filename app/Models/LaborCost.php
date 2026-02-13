<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class LaborCost extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'company_id',
        'role',
        'monthly_salary',
        'iess_rate',
        'decimo_tercero_rate',
        'decimo_cuarto_rate',
        'vacation_rate',
        'fondo_reserva_rate',
        'severance_rate'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
