<?php

declare(strict_types=1);

namespace App\Http\Traits;

use App\Http\Scopes\SortingScope;
use Illuminate\Database\Eloquent\Builder;

trait HasSortingScopeTrait
{
    public function scopeSorting(Builder $query, array $params = [])
    {
        return SortingScope::apply($query, $params);
    }
}
