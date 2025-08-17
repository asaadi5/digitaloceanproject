<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyVideo extends Model
{
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
