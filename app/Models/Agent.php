<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function messageReplies()
    {
        return $this->hasMany(MessageReply::class);
    }
}
