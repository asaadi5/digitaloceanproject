<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // لو كنت تستخدم guarded بدلاً من fillable اتركه كما هو عندك
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'photo',
        'total_views',
        'type_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

}
