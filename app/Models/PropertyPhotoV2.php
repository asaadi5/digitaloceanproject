<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPhotoV2 extends Model
{
    protected $table = 'property_photos_v2';

    protected $fillable = ['property_v2_id', 'photo', 'caption', 'sort_order'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
