<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPriceHistoryV2 extends Model
{
    protected $table = 'property_price_history_v2';

    protected $fillable = ['property_v2_id', 'price', 'currency', 'changed_at', 'note'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
