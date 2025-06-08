<?php

namespace App\Models\Scopes;

use App\Traits\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MyScope implements Scope
{
    use Utilities;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('user_id', $this->my('id'));
    }
}
