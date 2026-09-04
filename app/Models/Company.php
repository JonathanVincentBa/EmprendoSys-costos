<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\Sale;
use App\Models\LaborCost; // 👈 Añade esta línea

class Company extends Model
{
    protected $fillable = [
        'name',
        'razon_social',
        'ruc',
        'address',
        'establishment_address',
        'phone',
        'email',
        'status',
        'logo',
        'estab',
        'pto_emi',
        'contribuyente_especial',
        'obligado_contabilidad',
        'contribuyente_rimpe',
        'signature_path',
        'signature_password',
        'sri_environment',
    ];

    public function getLogoUrlAttribute()
    {
        if ($this->logo && file_exists(storage_path('app/public/' . $this->logo))) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // 👇 Relación faltante: una empresa tiene muchos costos de mano de obra
    public function laborCosts()
    {
        return $this->hasMany(LaborCost::class);
    }

    public function overheadConfigs()
    {
        return $this->hasMany(OverheadConfig::class);
    }
    public function setSignaturePasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['signature_password'] = encrypt($value);
        }
    }

    public function getSignaturePasswordAttribute($value)
    {
        if (!empty($value)) {
            try {
                return decrypt($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
