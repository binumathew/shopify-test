<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySetting extends Model
{
     // use HasFactory;

    protected $fillable = [
        'shop_domain',
        'threshold_quantity',
        'alert_email',
    ];
}
