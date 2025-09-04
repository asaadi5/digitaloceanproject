<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // NEW
use App\Models\PropertyDocument;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'agent_id',
        'location_id',
        'area',
        'type_id',

        'name',
        'slug',
        'description',
        'price',
        'featured_photo',

        'purpose',          // بيع / إيجار أو ما يقابله لديك
        'bedroom',
        'bathroom',
        'size',
        'floor',
        'garage',
        'balcony',
        'address',
        'built_year',
        'map',

        'amenities',        // مخزنة كسلسلة مفصولة بفواصل

        'is_featured',      // yes | no (lowercase)
        'status',           // active | pending | rejected (lowercase)

        // حقول التحقق/القانونية (اختياري حسب سكيمتك)
        'registry_number',
        'registry_zone',
        'ownership_type',       // freehold/leasehold... الخ
        'zoning_class',
        'building_permit_no',
        'build_code_compliance',// tinyint/bool
        'earthquake_resistance',// tinyint/bool
        'legal_notes',
        'verification_status',  // pending|verified|rejected (lowercase)
        'moderation_notes',
    ];

    /**
     * التحويلات لأنواع البيانات
     */
    protected $casts = [
        'price'                   => 'float',
        'bedroom'                 => 'integer',
        'bathroom'                => 'integer',
        'size'                    => 'integer',
        'floor'                   => 'integer',
        'garage'                  => 'integer',
        'balcony'                 => 'integer',
        'build_code_compliance'   => 'boolean',
        'earthquake_resistance'   => 'boolean',
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
    ];

    /*****************
     * العلاقات
     *****************/

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class, 'property_id');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PropertyDocument::class, 'property_id');
    }

    public function videos()
    {
        return $this->hasMany(PropertyVideo::class, 'property_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /*****************
     * Scopes مفيدة
     *****************/

    // حالة العقار Active (lowercase متوافق مع الداتابيس)
    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    // عقارات مميّزة
    public function scopeFeatured($q) // NEW
    {
        return $q->where('is_featured', 'yes');
    }

    // عقارات لوكيل يملك باقة مفعلة وغير منتهية
    public function scopeAgentWithActiveOrder($q)
    {
        return $q->whereHas('agent', function ($agentQ) {
            $agentQ->whereHas('orders', function ($orderQ) {
                $orderQ->where('currently_active', 1)
                    ->where('status', 'Completed')
                    ->where('expire_date', '>=', now());
            });
        });
    }

    // نفس شروط "أحدث العقارات" مجموعة في سكوب واحد لإعادة الاستخدام
    public function scopePublicVisible($q) // NEW
    {
        return $q->active()->agentWithActiveOrder();
    }

    // عدّاد "مفضّل" للمستخدم المحدد (أو المستخدم الحالي إذا لم يتم تمرير ID)
    public function scopeWithWishlistedCountFor($q, $userId = null) // NEW
    {
        $uid = $userId ?? Auth::id();
        return $q->withCount(['wishlists as wishlisted' => function ($qq) use ($uid) {
            if ($uid) {
                $qq->where('user_id', $uid);
            } else {
                // لو ما في مستخدم مسجّل، خلّي العدّاد صفر دائماً
                $qq->whereRaw('1 = 0');
            }
        }]);
    }

    // تحميل العلاقات الشائعة
    public function scopeWithBasicIncludes($q) // NEW
    {
        return $q->with(['location','type','agent']);
    }

    /*****************
     * Mutators / Accessors
     *****************/

    // خزّن الحالة دائمًا lowercase
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = is_string($value) ? strtolower($value) : $value;
    }

    // خزّن الـ featured بشكل yes|no lowercase
    public function setIsFeaturedAttribute($value)
    {
        if (is_bool($value)) {
            $this->attributes['is_featured'] = $value ? 'yes' : 'no';
        } else {
            $this->attributes['is_featured'] = strtolower((string) $value);
        }
    }

    // تطبيع بسيط للغرض (يقبل العربية/الإنجليزية) — اختياري
    public function setPurposeAttribute($value) // NEW (اختياري)
    {
        $v = trim(mb_strtolower((string) $value));
        // حوّل مرادفات إلى قيم موحّدة
        if (in_array($v, ['rent','إيجار','ايجار'])) $v = 'إيجار';
        if (in_array($v, ['sale','بيع'])) $v = 'بيع';
        $this->attributes['purpose'] = $v;
    }

    // Accessor مساعد: يرجّع لائحة الـ amenities كـ array بدون ما يغيّر التخزين الأصلي
    public function getAmenitiesListAttribute()
    {
        $raw = $this->attributes['amenities'] ?? '';
        if ($raw === '' || $raw === null) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    // تنسيق سعر جاهز للعرض
    public function getPriceFormattedAttribute() // NEW
    {
        return number_format((float)($this->attributes['price'] ?? 0), 0, '.', ',');
    }
}
