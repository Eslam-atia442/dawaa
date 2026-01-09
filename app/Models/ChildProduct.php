<?php

namespace App\Models;

class ChildProduct extends Product
{
    // This model extends Product and is used for permissions and type distinction
    // All child products are stored in the products table with parent_id set

    protected $table = 'products';

    protected static function boot()
    {
        parent::boot();
        
        // Automatically scope to child products
        static::addGlobalScope('children', function ($query) {
            $query->whereNotNull('parent_id');
        });
    }
}
