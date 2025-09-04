<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['post_id', 'author_name', 'author_email', 'body', 'approved'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
