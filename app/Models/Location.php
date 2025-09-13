<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function properties()
    {
        // العلاقة الخام بدون شروط
        return $this->hasMany(\App\Models\Property::class, 'location_id');
    }

    public function publicProperties()
    {
        // نفس العلاقة لكن مع فلترة الظهور العام عبر الـ Scope
        return $this->hasMany(\App\Models\Property::class, 'location_id')
            ->publicVisible();
    }
}
