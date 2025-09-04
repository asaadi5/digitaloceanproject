<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPriceHistory extends Model
{
    protected $table = 'property_price_history';

    protected $fillable = [
        'property_id','purpose','price','currency',
        'effective_from','effective_to','reason','changed_by'
    ];

    protected $casts = [
        'effective_from' => 'datetime',
        'effective_to'   => 'datetime',
    ];

    public function property() { return $this->belongsTo(Property::class); }
}
