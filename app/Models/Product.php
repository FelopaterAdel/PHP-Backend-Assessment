<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use SoftDeletes,HasUuid;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'stock_quantity',
        'low_stock_threshold',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
      ];
}
