<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RentalBooking extends Model
{
    protected $fillable = [
        'property_id','user_id','start_at','end_at','price','status','notes'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    // علاقات
    public function property() { return $this->belongsTo(Property::class); }
    public function user()     { return $this->belongsTo(User::class); }

    // نطاق: الحجوزات المتداخلة مع فترة معينة
    public function scopeOverlapping(Builder $q, $propertyId, $start, $end): Builder
    {
        return $q->where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->where(function($qq) use ($start,$end) {
                $qq->where('start_at', '<', $end)
                    ->where('end_at',   '>', $start);
            });
    }

    // فحص سريع للتداخل
    public static function hasConflict($propertyId, $start, $end): bool
    {
        return static::overlapping($propertyId, $start, $end)->exists();
    }
}
