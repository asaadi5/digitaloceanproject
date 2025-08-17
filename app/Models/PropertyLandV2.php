<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyLandV2 extends Model
{
    protected $table = 'property_lands_v2';

    protected $fillable = [
        'property_v2_id', 'land_area_m2', 'land_type', 'zoning', 'slope_percentage',
        'water_source', 'electricity_available', 'road_access', 'fencing', 'notes'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
