<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyVideoV2 extends Model
{
    protected $table = 'property_videos_v2';

    protected $fillable = ['property_v2_id', 'video_url', 'caption'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
