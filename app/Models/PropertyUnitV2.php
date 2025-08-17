<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyUnitV2 extends Model
{
    protected $table = 'property_units_v2';

    protected $fillable = [
        'property_v2_id', 'unit_name', 'unit_size_m2', 'bedroom', 'bathroom', 'price', 'currency'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
