<?php

namespace App\Traits;

trait SearchTrait
{
    public function scopeOfKeyword($query, $keyword)
    {
         $columns = implode(',', $this->searchable ?? []);
        if (empty($keyword) || empty($columns)) {
            return $query;
        }

        return $query->whereRaw("CONCAT_WS(' ', {$columns}) like '%{$keyword}%'");
    }
    public function scopeOfHas($query, $relations)
    {
        foreach((array)$relations as $relation){
            $query->has($relation);
        }
        return $query;
    }
    public function scopeOfCreatedAtMin($query, $value): mixed
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeOfCreatedAtMax($query, $value): mixed
    {
        return $query->where('created_at', '<=', $value);
    }
}
