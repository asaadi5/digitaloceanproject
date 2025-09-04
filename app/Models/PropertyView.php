<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyView extends Model
{
    protected $fillable = [
        'property_id','user_id','ip','session_id','user_agent',
        'viewer_hash','viewed_on','views'
    ];

    public function property() {
        return $this->belongsTo(Property::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    /** سكوبات مساعدة */
    public function scopeToday($q) {
        return $q->whereDate('viewed_on', now()->toDateString());
    }
    public function scopeForViewer($q, string $hash) {
        return $q->where('viewer_hash', $hash);
    }
}
