<?php

namespace App\Models;

use App\Models\Scopes\MyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Athlete extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function stravaCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $dt = Carbon::parse($value)->format('D M j, Y'),
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $dt = Carbon::parse($value)->format('j/n/Y g:i:s a'),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new MyScope);
    }
}
