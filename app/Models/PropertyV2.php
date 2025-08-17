<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyV2 extends Model
{
    protected $table = 'properties_v2';

    protected $fillable = [
        'agent_id', 'location_id', 'type_id', 'amenities', 'name', 'slug', 'description',
        'price', 'price_currency', 'price_type', 'price_note', 'featured_photo', 'purpose',
        'rent_period', 'deposit_amount', 'furnished', 'pets_allowed',
        'bedroom', 'bathroom', 'size', 'area_m2', 'floor', 'total_floors',
        'garage', 'parking_spaces', 'balcony', 'elevator',
        'address', 'city', 'neighborhood', 'street',
        'built_year', 'finishing', 'structure', 'heating', 'ac', 'orientation', 'view', 'utilities',
        'legal_title', 'legal_flags', 'title_notes', 'map', 'geo_lat', 'geo_lng',
        'is_featured', 'status'
    ];

    public function photos()
    {
        return $this->hasMany(PropertyPhotoV2::class, 'property_v2_id');
    }

    public function videos()
    {
        return $this->hasMany(PropertyVideoV2::class, 'property_v2_id');
    }

    public function amenities()
    {
        return $this->hasMany(PropertyAmenityV2::class, 'property_v2_id');
    }

    public function documents()
    {
        return $this->hasMany(PropertyDocumentV2::class, 'property_v2_id');
    }

    public function priceHistory()
    {
        return $this->hasMany(PropertyPriceHistoryV2::class, 'property_v2_id');
    }

    public function meta()
    {
        return $this->hasMany(PropertyMetaV2::class, 'property_v2_id');
    }

    public function units()
    {
        return $this->hasMany(PropertyUnitV2::class, 'property_v2_id');
    }

    public function nearby()
    {
        return $this->hasMany(PropertyNearbyV2::class, 'property_v2_id');
    }
}
