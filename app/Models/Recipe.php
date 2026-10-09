<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use App\Services\CostCalculatorService;

class Recipe extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'product_id', 'description', 'batch_size_ml'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function items()
    {
        return $this->hasMany(RecipeItem::class);
    }
    public function processes()
    {
        return $this->belongsToMany(
            ProductionProcess::class,
            'recipe_processes',
            'recipe_id',
            'process_id'
        )
            ->withPivot('hours_per_batch');
    }
    public function supplyUsages()
    {
        return $this->hasMany(SupplyUsage::class);
    }
    public function packagingMaterials()
    {
        return $this->belongsToMany(PackagingMaterial::class, 'recipe_packaging')
            ->withPivot('units_per_batch')
            ->withTimestamps();
    }
    // Costo de Materias Primas en el lote
    public function getIngredientsCostAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity_kg * ($item->rawMaterial?->unit_cost ?? 0);
        });
    }

    // Costo de Mano de Obra basado en los procesos
    public function getLaborCostAttribute()
    {
        return app(CostCalculatorService::class)->calculateLaborCost($this);
    }
}
