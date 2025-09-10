<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Agent, Location, Type, Amenity, Wishlist,
    PropertyPhoto, PropertyVideo, PropertyDocument
};

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    /**
     * الحقول القابلة للتعبئة
     * (حرصت أضيف كل الأعمدة المستخدمة في الكونترولرات + اللي ظهرت في صورة DB)
     */
    protected $fillable = [
        'agent_id',
        'name',
        'slug',
        'price',
        'description',
        'location_id',
        'type_id',
        'purpose',
        'area',
        'address',
        'bedroom',
        'bathroom',
        'size',
        'floor',
        'garage',
        'balcony',
        'built_year',
        'is_featured',
        'featured_photo',
        'map',

        // حالة العرض والمتابعة
        'status',
        'total_views',

        // في بعض الأماكن تُخزَّن كقائمة IDs مفصولة بفواصل
        'amenities',

        // حقول السجل/الترخيص/الملكية/التصنيف
        'registry_number',
        'registry_zone',
        'ownership_type',
        'zoning_class',
        'building_permit_no',

        // مطابقة الكود الزلزالي والملاحظات القانونية
        'build_code_compliance',
        'earthquake_resistance',
        'legal_notes',
    ];

    /**
     * التحويلات
     */
    protected $casts = [
        'price'                 => 'float',
        'bedroom'               => 'integer',
        'bathroom'              => 'integer',
        'size'                  => 'integer',
        'floor'                 => 'integer',
        'garage'                => 'integer',
        'balcony'               => 'integer',
        'built_year'            => 'integer',
        'total_views'           => 'integer',
        'build_code_compliance' => 'boolean',
        'earthquake_resistance' => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    /**
     * خصائص محسوبة تظهر تلقائياً مع الـ JSON
     */
    protected $appends = [
        'featured_photo_url',
        'price_formatted',
        'is_featured_bool',
        'amenities_list',
    ];

    /***************************************************************************
     * العلاقات
     ***************************************************************************/

    public function agent()     { return $this->belongsTo(Agent::class, 'agent_id'); }
    public function location()  { return $this->belongsTo(Location::class); }
    public function type()      { return $this->belongsTo(Type::class); }

    public function wishlists() { return $this->hasMany(Wishlist::class); }

    public function photos()    { return $this->hasMany(PropertyPhoto::class); }
    public function videos()    { return $this->hasMany(PropertyVideo::class); }
    public function documents() { return $this->hasMany(PropertyDocument::class, 'property_id'); }

    // Many-to-Many مع الـ amenities عبر جدول pivot amenity_property
    public function amenitiesRelation()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_property', 'property_id', 'amenity_id')
            ->withTimestamps();
    }

    /***************************************************************************
     * Scopes مفيدة لإعادة الاستخدام في الكونترولرات
     ***************************************************************************/

    // حالة العقار Active (lowercase كما في قاعدة البيانات)
    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }
    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }


    // عقارات مميّزة (yes/no lowercase)
    public function scopeFeatured($q)
    {
        return $q->where('is_featured', 'yes');
    }

    // عقارات لوكيل لديه باقة مفعلة وغير منتهية
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

    // نفس شروط الظهور العام على الويب
    public function scopePublicVisible($q)
    {
        return $q->active()->agentWithActiveOrder();
    }

    // عدّاد "مفضّل" للمستخدم المحدد (أو الحالي)
    public function scopeWithWishlistedCountFor($q, $userId = null)
    {
        $uid = $userId ?? Auth::id();
        return $q->withCount(['wishlists as wishlisted' => function ($qq) use ($uid) {
            if ($uid) {
                $qq->where('user_id', $uid);
            } else {
                // بدون مستخدم مسجّل: ارجِع 0 دائماً
                $qq->whereRaw('1 = 0');
            }
        }]);
    }

    // تحميل العلاقات الشائعة
    public function scopeWithBasicIncludes($q)
    {
        return $q->with(['location','type','agent']);
    }

    /***************************************************************************
     * Mutators / Accessors
     ***************************************************************************/

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

    // تطبيع الغرض للإنجليزية الموحّدة (sale|rent|wanted)
    public function setPurposeAttribute($value)
    {
        $v = trim(mb_strtolower((string) $value));
        if (in_array($v, ['rent','إيجار','ايجار'])) $v = 'rent';
        elseif (in_array($v, ['sale','بيع','buy'])) $v = 'sale';
        elseif (in_array($v, ['wanted','مطلوب']))    $v = 'wanted';
        $this->attributes['purpose'] = $v;
    }

    // URL للصورة المميّزة من مجلد uploads
    public function getFeaturedPhotoUrlAttribute(): ?string
    {
        return $this->featured_photo ? url('uploads/'.$this->featured_photo) : null;
    }

    // تنسيق السعر
    public function getPriceFormattedAttribute(): string
    {
        return number_format((float)($this->attributes['price'] ?? 0), 0, '.', ',');
    }

    // تحويل is_featured إلى Boolean للعرض
    public function getIsFeaturedBoolAttribute(): bool
    {
        return strtolower((string)($this->attributes['is_featured'] ?? 'no')) === 'yes';
    }

    // إرجاع قائمة amenities المخزنة كنص CSV (إن وجدت)
    public function getAmenitiesListAttribute(): array
    {
        $raw = $this->attributes['amenities'] ?? '';
        if ($raw === '' || $raw === null) return [];
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /***************************************************************************
     * ملاحظات مهمة
     * - لاستعمال الـ pivot الحقيقي للميزات، استخدم العلاقة amenitiesRelation().
     * - الكونترولرات الحالية تضيف أحياناً IDs كـ CSV في عمود properties. وجود
     *   amenitiesRelation يسمح لك لاحقاً تعمل sync() للـ pivot بدون كسر الخلفية.
     ***************************************************************************/
}
