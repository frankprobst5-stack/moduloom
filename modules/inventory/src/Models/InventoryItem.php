<?php

namespace Moduloom\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'sku', 'quantity', 'reorder_threshold'];
}
