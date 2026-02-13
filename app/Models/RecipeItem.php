<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'recipe_id', 'raw_material_id', 'quantity_kg'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
