<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyNearbyV2 extends Model
{
    protected $table = 'property_nearby_v2';

    protected $fillable = ['property_v2_id', 'place_name', 'place_type', 'distance_m'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
