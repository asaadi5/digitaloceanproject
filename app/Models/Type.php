<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
