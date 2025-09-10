<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $table = 'wishlists';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'property_id',
    ];

    /* علاقات */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /* سكوبات مفيدة */
    public function scopeForUser($q, $userId)
    {
        return $q->where('user_id', $userId);
    }
}
