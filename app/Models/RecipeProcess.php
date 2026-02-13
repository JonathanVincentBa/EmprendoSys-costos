<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class RecipeProcess extends Model
{
    use BelongsToCompany;
    protected $fillable = ['recipe_id', 'production_process_id', 'sequence'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
    public function productionProcess()
    {
        return $this->belongsTo(ProductionProcess::class);
    }
}
