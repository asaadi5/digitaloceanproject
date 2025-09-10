<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPhoto extends Model
{
    protected $fillable = [
    'property_id', 'photo'
];
    public function propertyPhoto()
    {

        return $this->belongsTo(Property::class);
    }
}
