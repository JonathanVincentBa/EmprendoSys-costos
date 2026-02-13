<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * El "boot" del trait se ejecuta automáticamente en el modelo que lo use.
     */
    protected static function bootBelongsToCompany(): void
    {
        // 1. Al crear un registro, asignamos automáticamente el company_id del usuario logueado
        static::creating(function ($model) {
            if (Auth::check() && ! $model->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });

        // 2. Filtro Global: Todas las consultas SQL incluirán "WHERE company_id = X"
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                // Si el usuario es super-admin, saltamos el filtro para que pueda ver TODO
                if (! $user->hasRole('super-admin')) {
                    $builder->where('company_id', $user->company_id);
                }
            }
        });
    }

    /**
     * Relación con la empresa (opcional, pero útil)
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
