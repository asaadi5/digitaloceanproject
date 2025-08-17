<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMetaV2 extends Model
{
    protected $table = 'property_meta_v2';

    protected $fillable = ['property_v2_id', 'meta_key', 'meta_value'];

    public function property()
    {
        return $this->belongsTo(PropertyV2::class, 'property_v2_id');
    }
}
