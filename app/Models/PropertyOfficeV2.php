<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyOfficeV2 extends Model
{
    protected $table = 'property_offices_v2';

    protected $fillable = [
        'property_v2_id', 'office_area_m2', 'number_of_rooms', 'conference_room',
        'furnished', 'internet_available', 'air_conditioning', 'security_system', 'notes'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
