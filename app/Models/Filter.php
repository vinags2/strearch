<?php

namespace App\Models;

use App\Models\Scopes\MyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'type_of_name_search', 'updated_at', 'user_id'];

    // protected function casts(): array
    // {
    //     return [
    //         'filter' => 'array',
    //     ];
    // }

    public static function active_filter_as_collection()
    {
        return Filter::where('active', 1)->first();
    }

    public static function active_filter()
    {
        return Filter::where('active', 1)->first()?->filter;
    }

    public static function active_filter_id()
    {
        return Filter::where('active', 1)->first()?->id;
    }

    public static function filters_names()
    {
        return Filter::all(['id', 'name', 'active']);
    }

    public static function set_all_to_inactive()
    {
        Filter::where('active', 1)
            ->update(['active' => 0]);
    }

    public static function set_active($id)
    {
        Filter::set_all_to_inactive();
        Filter::where('id', $id)
            ->update(['active' => 1]);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new MyScope);
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('name');
        });
    }
}
