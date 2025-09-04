<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'types';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'booking_enabled',
        'image',

    ];

    protected $casts = [
        'parent_id'       => 'integer',
        'booking_enabled' => 'boolean',
    ];

    /*****************
     * العلاقات
     *****************/

    // النوع الأب
    public function parent()
    {
        return $this->belongsTo(Type::class, 'parent_id');
    }

    // الأنواع الأبناء
    public function children()
    {
        return $this->hasMany(Type::class, 'parent_id');
    }

    // العقارات المرتبطة بهذا النوع
    public function properties()
    {
        return $this->hasMany(Property::class, 'type_id');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
