<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAmenityV2 extends Model
{
    protected $table = 'property_amenity_v2';

    protected $fillable = ['property_v2_id', 'amenity_id'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
