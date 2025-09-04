<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyRentalRule extends Model
{
    protected $fillable = [
        'property_id','rule_key','rule_value','is_enforced','notes'
    ];

    public function property() { return $this->belongsTo(Property::class); }
}
