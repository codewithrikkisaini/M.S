<?php

namespace App\Traits;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->hotel_id) && Auth::user()->hotel_id) {
                $model->hotel_id = Auth::user()->hotel_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::hasUser()) {
                $user = Auth::user();
                if ($user && $user->hotel_id) {
                    $builder->where($builder->getModel()->getTable() . '.hotel_id', $user->hotel_id);
                }
            } elseif (static::class !== \App\Models\User::class) {
                if (Auth::check() && Auth::user()->hotel_id) {
                    $builder->where($builder->getModel()->getTable() . '.hotel_id', Auth::user()->hotel_id);
                }
            }
        });
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
