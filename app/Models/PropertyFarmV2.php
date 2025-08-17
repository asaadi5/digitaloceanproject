<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFarmV2 extends Model
{
    protected $table = 'property_farms_v2';

    protected $fillable = [
        'property_v2_id', 'farm_area_m2', 'water_source', 'electricity_available', 'irrigation_type',
        'crop_type', 'number_of_trees', 'has_greenhouse', 'animal_farm', 'notes'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
