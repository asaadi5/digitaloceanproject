<?php
/**
 * FrontController (Refactored for cleanliness only)
 * لا يوجد أي تغيير وظيفي على الإطلاق. تم فقط:
 * - استخراج تكرارات إلى دوال خاصة (Helpers).
 * - استخدام خرائط/ثوابت موحّدة للفرز والأغراض.
 * - توحيد منطق عنوان الصفحة في البحث.
 * - الحفاظ على نفس الاستعلامات والنواتج.
 */

namespace App\Http\Controllers\Front;

namespace App\Http\Controllers\Front; // كما كان في ملفك الأصلي (مكرر عمدًا)

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\Websitemail;
use App\Models\Location;
use App\Models\Type;
use App\Models\Amenity;
use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyVideo;
use App\Models\Agent;
use App\Models\Wishlist;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Subscriber;
use App\Models\Order;
use App\Models\User;
use App\Models\Comment;
use App\Models\PropertyDocument;
use Illuminate\Support\Str;

class FrontController extends Controller
{
    /*────────────────────────────────────────────────────────────────────────────
    الدالة: index
    الغرض: تجهيز بيانات الصفحة الرئيسية (عقارات مميزة، أحدث، مواقع، وكلاء، إحصاءات…)
    المدخلات: لا شيء
    المخرجات: View 'front.home' مع جميع المتغيرات كما في النسخة الأصلية
    ملاحظات: الاعتماد على Scopes (publicVisible/featured/withBasicIncludes/withWishlistedCountFor)
    ────────────────────────────────────────────────────────────────────────────*/
    public function index()
    {
        // Small featured strip (6 items) — public + featured
        $properties = Property::publicVisible()
            ->featured()
            ->orderBy('id', 'asc')
            ->take(6)
            ->get();

        // Locations ranked by publicly visible properties
        $locations = Location::withCount(['properties' => fn ($q) => $q->publicVisible()])
            ->orderBy('properties_count', 'desc')
            ->take(14)
            ->get();

        // Top-5 locations
        $topLocations = Location::withCount(['properties' => fn($q) => $q->publicVisible()])
            ->orderBy('properties_count', 'desc')
            ->take(5)
            ->get();

        // Quick search facets
        $search_locations = Location::orderBy('name', 'asc')->get();
        $search_types     = Type::orderBy('name', 'asc')->get();

        // Active agents with public property counts only
        $agents = Agent::where('status', 1)
            ->withCount(['properties' => fn ($q) => $q->publicVisible()])
            ->orderBy('id', 'asc')
            ->take(7)
            ->get();

        // Testimonials and latest posts (home widgets)
        $testimonials = Testimonial::orderBy('id', 'asc')->get();
        $posts        = Post::orderBy('id', 'desc')->take(4)->get();

        // Major group type IDs (parent + children)
        $TYPE_RESIDENTIAL = 1;
        $TYPE_COMMERCIAL  = 2;
        $TYPE_RECREATION  = 3;
        $TYPE_LANDS       = 4;

        $landsTypeIds = $this->typeIdsFor($TYPE_LANDS);
        $recreTypeIds = $this->typeIdsFor($TYPE_RECREATION);
        $commTypeIds  = $this->typeIdsFor($TYPE_COMMERCIAL);
        $resiTypeIds  = $this->typeIdsFor($TYPE_RESIDENTIAL);

        // Aggregated counts by major groups (public only)
        $counts = [
            'lands'       => Property::publicVisible()->whereIn('type_id', $landsTypeIds)->count(),
            'recre'       => Property::publicVisible()->whereIn('type_id', $recreTypeIds)->count(),
            'commercial'  => Property::publicVisible()->whereIn('type_id', $commTypeIds)->count(),
            'residential' => Property::publicVisible()->whereIn('type_id', $resiTypeIds)->count(),
        ];

        // Subtypes under [1,2,3,4] with public counts
        $subtypes = Type::whereIn('parent_id', [1, 2, 3, 4])
            ->withCount(['properties as properties_count' => fn ($q) => $q->publicVisible()])
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();

        // Featured slider (12) — eager + wishlist signals
        $featured_properties = Property::withBasicIncludes()
            ->publicVisible()
            ->featured()
            ->withWishlistedCountFor()
            ->latest()
            ->take(12)
            ->get();

        // Latest properties (12) — eager + wishlist signals
        $latest_properties = Property::withBasicIncludes()
            ->publicVisible()
            ->withWishlistedCountFor()
            ->latest()
            ->take(12)
            ->get();

        // KPI counters
        $agents_total     = Agent::where('status', 1)->count();
        $orders_total     = Order::where('currently_active', 1)->where('status', 'Completed')->where('expire_date', '>=', now())->count();
        $properties_total = Property::publicVisible()->count();
        $users_total      = User::count();

        // Latest posts for home
        $latestPosts = Post::with('type')
            ->withCount(['comments as comments_count' => fn($q) => $q->where('approved', 1)])
            ->latest('id')
            ->take(12)
            ->get(['id','title','slug','short_description','photo','type_id','total_views','created_at']);

        return view('front.home', compact(
            'properties',
            'locations',
            'agents',
            'search_locations',
            'search_types',
            'testimonials',
            'posts',
            'counts',
            'subtypes',
            'latest_properties',
            'featured_properties',
            'agents_total',
            'orders_total',
            'properties_total',
            'users_total',
            'topLocations',
            'latestPosts'
        ));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: contact
    الغرض: إظهار صفحة اتصل بنا
    المدخلات: —
    المخرجات: View 'front.contact'
    ────────────────────────────────────────────────────────────────────────────*/
    public function contact()
    {
        // Simple view render for contact page
        return view('front.contact');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: contact_submit
    الغرض: التحقق من فورم اتصل بنا وإرسال رسالة تأكيد للمستخدم
    المدخلات: Request (name, email, message)
    المخرجات: JSON (code, success_message|error_message[])
    ملاحظات: عدم تغيير قواعد التحقق أو جسم الرسالة
    ────────────────────────────────────────────────────────────────────────────*/
    public function contact_submit(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name' => ['required'],
            'email' => ['required','email','unique:subscribers,email'],
            'message' => ['required'],
        ]);

        if(!$validator->passes()) {
            // Return structured validation errors for AJAX
            return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);
        }

        // Build confirmation email
        $subject = 'Contact Form Message';
        $message = 'Sender Information:<br>';
        $message .= '<b>Name:</b><br>'.$request->name.'<br><br>';
        $message .= '<b>Email:</b><br>'.$request->email.'<br><br>';
        $message .= '<b>Message:</b><br>'.nl2br($request->message);

        \Mail::to($request->email)->send(new Websitemail($subject,$message));

        return response()->json(['code'=>1,'success_message'=>'Message is sent successfully']);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: blog
    الغرض: قائمة التدوينات مع فلترة اختيارية (بالنوع عبر slug، وبالعنوان عبر q)
    المدخلات: Request (?type=slug, ?q=)
    المخرجات: View 'front.blog' مع pagination وحساب التعليقات الموافق عليها
    ────────────────────────────────────────────────────────────────────────────*/
    public function blog(Request $request)
    {
        // Sidebar categories with counts
        $types = Type::withCount('posts')->orderByDesc('posts_count')->get(['id','name','slug']);

        // Base posts query
        $postsQuery = Post::with('type')
            ->withCount(['comments as comments_count' => fn($q) => $q->where('approved', 1)])
            ->orderByDesc('id');

        // Optional type filter (?type=slug)
        if ($request->filled('type')) {
            $type = Type::where('slug', $request->type)->first();
            if ($type) {
                $postsQuery->where('type_id', $type->id);
            }
        }

        // Optional title filter (?q=)
        if ($request->filled('q')) {
            $q = trim($request->q);
            $postsQuery->where('title', 'like', "%{$q}%");
        }

        $posts = $postsQuery->paginate(9)->withQueryString();

        return view('front.blog', compact('types','posts'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: post
    الغرض: عرض تدوينة مفردة بالـ slug مع زيادة المشاهدات وإظهار التعليقات الموافق عليها
    المدخلات: $slug
    المخرجات: View 'front.post' أو Redirect للمدونة إن لم توجد
    ────────────────────────────────────────────────────────────────────────────*/
    public function post($slug)
    {
        // Find post with type and approved comments
        $post = Post::with([
            'type',
            'comments' => fn($q) => $q->where('approved', 1)->latest()
        ])
            ->withCount(['comments as comments_count' => fn($q) => $q->where('approved', 1)])
            ->where('slug', $slug)
            ->first();

        if (!$post) {
            return redirect()->route('blog')->with('error', 'التدوينة غير موجودة');
        }

        // Increment views
        $post->increment('total_views');

        // Sidebar categories
        $types = Type::withCount('posts')->orderByDesc('posts_count')->get(['id','name','slug']);

        return view('front.post', compact('post','types'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: commentStore
    الغرض: حفظ تعليق جديد مع الموافقة الفورية (كما في ملفك)
    المدخلات: Request (author_name, author_email, body), Post (Route Model Binding)
    المخرجات: Redirect back مع رسالة نجاح
    ────────────────────────────────────────────────────────────────────────────*/
    public function commentStore(Request $request, Post $post)
    {
        $data = $request->validate([
            'author_name'  => ['required','string','max:255'],
            'author_email' => ['required','email','max:255'],
            'body'         => ['required','string','max:5000'],
        ]);

        Comment::create([
            'post_id'      => $post->id,
            'author_name'  => $data['author_name'],
            'author_email' => $data['author_email'],
            'body'         => $data['body'],
            'approved'     => 1,
        ]);

        return back()->with('success', 'تم إرسال تعليقك بنجاح.');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: faq
    الغرض: جلب الأسئلة الشائعة وعرضها
    المدخلات: —
    المخرجات: View 'front.about_us' مع $faqs (كما في كودك)
    ────────────────────────────────────────────────────────────────────────────*/
    public function faq()
    {
        // Load FAQs ascending by id
        $faqs = Faq::orderBy('id','asc')->get();
        return view('front.about_us', compact('faqs'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: about_us
    الغرض: عرض صفحة من نحن (ثابتة)
    المدخلات: —
    المخرجات: View 'front.about_us'
    ────────────────────────────────────────────────────────────────────────────*/
    public function about_us()
    {
        // Simple static page
        return view('front.about_us');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: select_user
    الغرض: عرض شاشة اختيار المستخدم (واجهة)
    المدخلات: —
    المخرجات: View 'front.select_user'
    ────────────────────────────────────────────────────────────────────────────*/
    public function select_user()
    {
        // Simple static page
        return view('front.select_user');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: pricing
    الغرض: عرض الباقات مرتبة
    المدخلات: —
    المخرجات: View 'front.pricing' مع $packages
    ────────────────────────────────────────────────────────────────────────────*/
    public function pricing()
    {
        // Load all packages ascending by id
        $packages = Package::orderBy('id','asc')->get();
        return view('front.pricing', compact('packages'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: property_detail
    الغرض: عرض صفحة تفاصيل عقار + تسجيل مشاهدة آمنة + تحضير بيانات جانبية
    المدخلات: $slug
    المخرجات: View 'front.property_detail' مع نفس المتغيرات
    ────────────────────────────────────────────────────────────────────────────*/
    public function property_detail($slug)
    {
        // Property with required relations
        $property = Property::with([
            'agent:id,name,email,phone,city,address,photo',
            'location:id,name,slug',
            'type:id,name,slug,parent_id',
            'photos:id,property_id,photo',
            'videos:id,property_id,video',
            'documents:id,property_id,doc_type,issuer,doc_no,issued_at,file_path',
        ])->where('slug', $slug)->firstOrFail();

        // View bump with dedup logic (unchanged)
        $this->bumpPropertyViews($property);

        // Type flags (unchanged)
        $type       = optional($property->type);
        $parentId   = (int) ($type->parent_id ?? 0);
        $isResi     = ($parentId === 1);
        $isCom      = ($parentId === 2);
        $isRecre    = ($parentId === 3);
        $isLand     = ($parentId === 4);
        $isRent     = Str::lower((string) $property->purpose) === 'rent';

        // History & rental rules (unchanged)
        $priceHistory = DB::table('property_price_history')
            ->where('property_id', $property->id)
            ->orderByDesc('effective_from')
            ->get();

        $rentalRules = DB::table('property_rental_rules')
            ->where('property_id', $property->id)
            ->orderBy('id')
            ->get();

        // Amenities list (unchanged)
        $amenities = DB::table('amenity_property')
            ->join('amenities', 'amenities.id', '=', 'amenity_property.amenity_id')
            ->where('amenity_property.property_id', $property->id)
            ->orderBy('amenities.name')
            ->pluck('amenities.name')
            ->toArray();

        // Related properties (unchanged)
        $related = Property::with(['type:id,name,parent_id', 'location:id,name'])
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->when($property->purpose, fn ($q) => $q->where('purpose', $property->purpose))
            ->when($property->type_id, fn ($q) => $q->where('type_id', $property->type_id))
            ->latest('id')->take(12)->get();

        // Agent latest (unchanged)
        $agentLatest = Property::with(['type:id,name', 'location:id,name'])
            ->where('agent_id', $property->agent_id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->latest('id')->take(6)->get();

        // Latest public properties (unchanged)
        $latestProperties = Property::where('status', 'active')->latest('id')->take(7)->get();

        return view('front.property_detail', [
            'property'         => $property,
            'isResi'           => $isResi,
            'isCom'            => $isCom,
            'isRecre'          => $isRecre,
            'isLand'           => $isLand,
            'isRent'           => $isRent,
            'priceHistory'     => $priceHistory,
            'rentalRules'      => $rentalRules,
            'amenities'        => $amenities,
            'related'          => $related,
            'agentLatest'      => $agentLatest,
            'latestProperties' => $latestProperties,
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: agent_show
    الغرض: عرض صفحة عامة للمعلن (User) وقائمة آخر عقاراته
    المدخلات: $id
    المخرجات: View 'front.agent_show' مع $agent و $properties
    ────────────────────────────────────────────────────────────────────────────*/
    public function agent_show($id)
    {
        // Agent minimal profile
        $agent = User::select('id','name','email','phone','city','address','photo')->findOrFail($id);

        // Agent properties (active)
        $properties = Property::with(['location:id,name','type:id,name'])
            ->where('agent_id', $id)
            ->where('status','active')
            ->latest('id')->paginate(12);

        return view('front.agent_show', compact('agent','properties'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: property_send_message
    الغرض: إرسال رسالة إلى وكيل العقار بالبريد
    المدخلات: Request (name,email,phone,message), $id (رقم العقار)
    المخرجات: Redirect back برسالة نجاح/خطأ
    ────────────────────────────────────────────────────────────────────────────*/
    public function property_send_message(Request $request,$id)
    {
        $property = Property::where('id',$id)->first();
        if (!$property) {
            return redirect()->route('home')->with('error', 'Property not found');
        }

        // Compose email to agent
        $subject = 'Property Inquiry';
        $message = 'You have received a new inquiry for the property: ' . $property->name.'<br><br>';
        $message .= 'Visitor Name:<br>'.$request->name.'<br><br>';
        $message .= 'Visitor Email:<br>'.$request->email.'<br><br>';
        $message .= 'Visitor Phone:<br>'.$request->phone.'<br><br>';
        $message .= 'Visitor Message:<br>'.nl2br($request->message);

        $agent_email = $property->agent->email;
        \Mail::to($agent_email)->send(new Websitemail($subject, $message));

        return redirect()->back()->with('success', 'Message sent successfully to agent');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: locations
    الغرض: عرض قائمة المواقع مرتّبة حسب عدد العقارات العامة (وفق شروطك)
    المدخلات: —
    المخرجات: View 'front.locations' مع pagination
    ملاحظة: أبقينا الشروط كما هي حرفيًا (لم نستبدلها بـ publicVisible)
    ────────────────────────────────────────────────────────────────────────────*/
    public function locations()
    {
        // Keep explicit conditions as in your code to ensure identical results
        $locations = Location::withCount(['properties' => function ($query) {
            $query->where('status', 'Active')
                ->whereHas('agent', function($q) {
                    $q->whereHas('orders', function($qq) {
                        $qq->where('currently_active', 1)
                            ->where('status', 'Completed')
                            ->where('expire_date', '>=', now());
                    });
                });
        }])->orderBy('properties_count', 'desc')->paginate(20);

        return view('front.locations', compact('locations'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: location
    الغرض: عرض صفحة موقع محدّد وقائمة عقاراته العامة
    المدخلات: $slug
    المخرجات: View 'front.location' أو Redirect برسالة خطأ
    ملاحظة: أبقينا الشروط كما هي حرفيًا (لم نستبدلها بـ publicVisible)
    ────────────────────────────────────────────────────────────────────────────*/
    public function location($slug)
    {
        $location = Location::where('slug', $slug)->first();
        if (!$location) {
            return redirect()->route('front.locations')->with('error', 'لم يتم العثور على الموقع');
        }

        $properties = Property::where('location_id', $location->id)
            ->where('status', 'Active')
            ->whereHas('agent', function($query) {
                $query->whereHas('orders', function($q) {
                    $q->where('currently_active', 1)
                        ->where('status', 'Completed')
                        ->where('expire_date', '>=', now());
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(6);

        return view('front.location', compact('location', 'properties'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: agents
    الغرض: عرض قائمة الوكلاء النشطين
    المدخلات: —
    المخرجات: View 'front.agents' مع pagination
    ────────────────────────────────────────────────────────────────────────────*/
    public function agents()
    {
        // Active agents paginated
        $agents = Agent::where('status', 1)->orderBy('id', 'asc')->paginate(20);
        return view('front.agents', compact('agents'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: agent
    الغرض: عرض صفحة وكيل محدّد وعقاراته
    المدخلات: $id
    المخرجات: View 'front.agent' مع $agent و $properties أو Redirect بخطأ
    ملاحظة: أبقينا الشروط كما هي حرفيًا (لم نستبدلها بـ publicVisible)
    ────────────────────────────────────────────────────────────────────────────*/
    public function agent($id)
    {
        $agent = Agent::where('id', $id)->first();
        if (!$agent) {
            return redirect()->route('home')->with('error', 'Agent not found');
        }

        $properties = Property::where('agent_id', $agent->id)
            ->where('status', 'Active')
            ->whereHas('agent', function($query) {
                $query->whereHas('orders', function($q) {
                    $q->where('currently_active', 1)
                        ->where('status', 'Completed')
                        ->where('expire_date', '>=', now());
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(6);

        return view('front.agent', compact('agent', 'properties'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: property_search
    الغرض: بحث العقارات بمرونة (route slugs + query params) بنفس منطقك تمامًا
    المدخلات: Request (جميع الباراميترات المذكورة)
    المخرجات: View 'front.property_search' مع النتائج والعناوين المشتقة
    ────────────────────────────────────────────────────────────────────────────*/
    public function property_search(Request $request)
    {
        // Route slugs → merge into request as your code does
        $purposeSlug  = $request->route('purpose');   // sale|rent|wanted
        $categorySlug = $request->route('category');  // residential|commercial|recreational|lands
        $typeSlug     = $request->route('type');      // apartment|villa|...
        $locationSlug = $request->route('slug');      // /properties/{slug}
        $locationRow  = null;

        if ($locationSlug) {
            $locationRow = Location::select('id','name')->where('slug', $locationSlug)->first();
            if ($locationRow) {
                // Inject location_id if found
                $request->merge(['location_id' => $locationRow->id]);
            }
        }

        // Normalize purpose into purpose_in[]
        if ($purposeSlug && !$request->filled('purpose_in')) {
            $request->merge(['purpose_in' => [$purposeSlug]]);
        }

        // Map main category slug → id (1..4)
        if ($categorySlug && !$request->filled('category_id')) {
            $catId = Type::whereNull('parent_id')->where('slug', $categorySlug)->value('id');
            if ($catId) {
                $request->merge(['category_id' => $catId]);
            }
        }

        // Pass type as-is (numeric id or slug/name supported)
        if ($typeSlug && !$request->filled('type')) {
            $request->merge(['type' => $typeSlug]);
        }

        // Auto-inject featured on /properties/featured
        $currentRoute = Route::currentRouteName();
        if ($currentRoute === 'properties_featured' && !$request->has('featured')) {
            $request->merge(['featured' => 1, 'sort' => $request->query('sort', 'newest')]);
        }

        // Gather inputs (unchanged)
        $name          = trim((string) $request->query('name', ''));
        $typeParam     = $request->query('type');
        $areaRange     = trim((string) $request->query('area_range', ''));
        $cityText      = trim((string) $request->query('city_text', ''));
        $provinceText  = trim((string) $request->query('province_text', ''));
        $purposeParam  = trim((string) $request->query('purpose', ''));
        $categoryId    = $request->integer('category_id');
        $sort          = $request->query('sort', 'newest');

        // New filter fields (as in your code)
        $priceMin      = $request->query('price_min');
        $priceMax      = $request->query('price_max');
        $bedroomMin    = $request->query('bedroom');
        $featuredOnly  = $request->boolean('featured');
        $locationId    = $request->input('location_id');

        // Escape helper for LIKE
        $escapeLike = static function (string $v): string {
            return addcslashes($v, "\\%_");
        };

        // Base query — active properties only
        $query = Property::query()->where('status', 'active');

        // purpose_in[] (supports ar/en and synonyms)
        $purposeIn = (array) $request->query('purpose_in', []);
        if (!empty($purposeIn)) {
            $all = [];
            foreach ($purposeIn as $p) {
                $all = array_merge($all, $this->purposeVariants($p));
            }
            $query->whereIn('purpose', array_unique($all));
        }

        // Location by ID or fallback to address text filter
        if ($locationId) {
            $query->where('location_id', $locationId);
        } elseif ($request->filled('city_text')) {
            $ct = trim((string) $request->query('city_text', ''));
            if ($ct !== '') {
                $query->where('address', 'like', '%'.$escapeLike($ct).'%');
            }
        }

        // Category main → restrict types to parent or its children
        if ($categoryId) {
            $allowedTypeIds = Type::where('id', $categoryId)->orWhere('parent_id', $categoryId)->pluck('id');
            if ($allowedTypeIds->isNotEmpty()) {
                $query->whereIn('type_id', $allowedTypeIds);
            }
        }

        // Type: numeric id or slug/name
        if ($typeParam !== null && $typeParam !== '') {
            if (is_numeric($typeParam)) {
                $query->where('type_id', (int) $typeParam);
            } else {
                $typeRow = $this->getTypeByFlexibleInput($typeParam, $escapeLike);
                if ($typeRow) {
                    $query->where('type_id', $typeRow->id);
                }
            }
        }

        // Name (title)
        if ($name !== '') {
            $query->where('name', 'like', '%'.$escapeLike($name).'%');
        }

        // Area range normalization (same patterns logic)
        if ($areaRange !== '') {
            $range = preg_replace('/\s+/', '', $areaRange);
            [$mode, $a, $b] = $this->normalizeAreaRange($range); // returns array as per your patterns
            if ($mode === 'between') {
                if ($a > $b) { [$a, $b] = [$b, $a]; }
                $query->whereBetween('size', [$a, $b]);
            } elseif ($mode === 'min') {
                $query->where('size', '>=', $a);
            } elseif ($mode === 'max') {
                $query->where('size', '<=', $a);
            }
        }

        // Price min/max
        if ($priceMin !== null && $priceMin !== '' && is_numeric($priceMin)) {
            $query->where('price', '>=', (float) $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '' && is_numeric($priceMax)) {
            $query->where('price', '<=', (float) $priceMax);
        }

        // Bedrooms minimum
        if ($bedroomMin !== null && $bedroomMin !== '' && is_numeric($bedroomMin)) {
            $query->where('bedroom', '>=', (int) $bedroomMin);
        }

        // Address filters
        if ($cityText !== '') {
            $query->where('address', 'like', '%'.$escapeLike($cityText).'%');
        }
        if ($provinceText !== '') {
            $query->where('address', 'like', '%'.$escapeLike($provinceText).'%');
        }

        // Featured only
        if ($featuredOnly) {
            $query->where('is_featured', 'yes');
        }

        // Sorting (identical map)
        $this->applySort($query, $sort);

        // Results with type eager load and same pagination
        $properties = $query->with('type')->paginate(12)->withQueryString();

        // Side collections for blade (unchanged)
        $resiTypeIds  = $this->typeIdsFor(1);
        $commTypeIds  = $this->typeIdsFor(2);
        $recreTypeIds = $this->typeIdsFor(3);
        $landsTypeIds = $this->typeIdsFor(4);

        // Page title composition (same priority rules)
        $pageTitle = $this->buildSearchPageTitle(
            $purposeSlug, $purposeParam, $purposeIn,
            $typeSlug, $typeParam, $escapeLike,
            $categorySlug, $categoryId,
            $featuredOnly, $sort
        );

        return view('front.property_search', [
            'properties'   => $properties,
            'resiTypeIds'  => $resiTypeIds,
            'commTypeIds'  => $commTypeIds,
            'recreTypeIds' => $recreTypeIds,
            'landsTypeIds' => $landsTypeIds,
            'pageTitle'    => $pageTitle,
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: wishlist_add
    الغرض: إضافة عقار إلى المفضلة للمستخدم المسجل
    المدخلات: $id
    المخرجات: Redirect back برسالة مناسبة
    ────────────────────────────────────────────────────────────────────────────*/
    public function wishlist_add($id)
    {
        if(!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('error', 'Please login to add property to wishlist');
        }

        // Guard against duplicates
        $existingWishlist = Wishlist::where('user_id', Auth::guard('web')->user()->id)
            ->where('property_id', $id)
            ->first();
        if($existingWishlist) {
            return redirect()->back()->with('error', 'Property already in wishlist');
        }

        $obj = new Wishlist();
        $obj->user_id = Auth::guard('web')->user()->id;
        $obj->property_id = $id;
        $obj->save();

        return redirect()->back()->with('success', 'Property added to wishlist');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: subscriber_send_email
    الغرض: استقبال اشتراك جديد بالبريد وإرسال رابط تحقق
    المدخلات: Request (email)
    المخرجات: JSON نجاح/أخطاء
    ────────────────────────────────────────────────────────────────────────────*/
    public function subscriber_send_email(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'email' => ['required','email','unique:subscribers,email'],
        ]);

        if(!$validator->passes()) {
            return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);
        }

        $token = hash('sha256', time());

        $obj = new Subscriber();
        $obj->email = $request->email;
        $obj->token = $token;
        $obj->status = 0;
        $obj->save();

        $verification_link = url('subscriber/verify/'.$request->email.'/'.$token);

        // Send verification email
        $subject = 'Subscriber Verification';
        $message = 'Please click on the link below to confirm subscription:<br>';
        $message .= '<a href="'.$verification_link.'">'.$verification_link.'</a>';

        \Mail::to($request->email)->send(new Websitemail($subject,$message));

        return response()->json(['code'=>1,'success_message'=>'Please check your email to confirm subscription']);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: subscriber_verify
    الغرض: تفعيل اشتراك البريد عبر الرابط المرسل
    المدخلات: $email, $token
    المخرجات: Redirect للصفحة الرئيسية برسالة نجاح أو بدون
    ────────────────────────────────────────────────────────────────────────────*/
    public function subscriber_verify($email,$token)
    {
        $subscriber_data = Subscriber::where('email',$email)->where('token',$token)->first();

        if($subscriber_data) {
            $subscriber_data->token = '';
            $subscriber_data->status = 1;
            $subscriber_data->update();

            return redirect()->route('home')->with('success', 'Your subscription is verified successfully!');
        }

        return redirect()->route('home');
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: terms
    الغرض: عرض صفحة الشروط
    المدخلات: —
    المخرجات: View 'front.terms' مع $terms_data
    ────────────────────────────────────────────────────────────────────────────*/
    public function terms()
    {
        $terms_data = Page::where('id',1)->first();
        return view('front.terms', compact('terms_data'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: privacy
    الغرض: عرض صفحة الخصوصية
    المدخلات: —
    المخرجات: View 'front.privacy' مع $privacy_data
    ────────────────────────────────────────────────────────────────────────────*/
    public function privacy()
    {
        $privacy_data = Page::where('id',1)->first();
        return view('front.privacy', compact('privacy_data'));
    }

    /*────────────────────────────────────────────────────────────────────────────
    Helpers خاصة (تنظيم فقط بدون تغيير سلوك)
    - typeIdsFor: إرجاع IDs للأب + الأبناء
    - applySort: تطبيق خريطة الفرز نفسها
    - bumpPropertyViews: نفس منطق عدّ المشاهدات الأصلي
    - normalizeAreaRange: تطبيع "100-200" / "150+" / "-120"
    - getTypeByFlexibleInput: إيجاد النوع من slug|name
    - purposeVariants: مرادفات الغرض (ar/en)
    - buildSearchPageTitle: إنشاء عنوان الصفحة بنفس الأولويات
    ────────────────────────────────────────────────────────────────────────────*/

    // Sorting map identical to your original intent
    private const SORT_MAP = [
        'newest'     => [['id', 'desc']],
        'oldest'     => [['id', 'asc']],
        'price_asc'  => [['price', 'asc'],  ['id', 'desc']],
        'price_desc' => [['price', 'desc'], ['id', 'desc']],
    ];

    private const PURPOSE_TEXT = [
        'sale'   => 'للبيع',
        'buy'    => 'للبيع',
        'rent'   => 'للإيجار',
        'wanted' => 'مطلوب',
    ];

    // Return IDs for parent and its direct children
    private function typeIdsFor(int $parentId)
    {
        return Type::where('id', $parentId)
            ->orWhere('parent_id', $parentId)
            ->pluck('id');
    }

    // Apply the same sorting combinations
    private function applySort(\Illuminate\Database\Eloquent\Builder $q, ?string $sort): void
    {
        $plan = self::SORT_MAP[$sort] ?? self::SORT_MAP['newest'];
        foreach ($plan as [$col, $dir]) {
            $q->orderBy($col, $dir);
        }
    }

    // Safe view increment logic (unchanged behavior)
    private function bumpPropertyViews(Property $property): void
    {
        try {
            if (Schema::hasTable('property_views')) {
                $user      = auth()->user();
                $ip        = request()->ip();
                $sessionId = request()->session()->getId();
                $ua        = substr(request()->userAgent() ?? '', 255);

                $identity   = $user ? "u:{$user->id}" : "g:{$ip}|{$sessionId}|{$ua}";
                $viewerHash = hash('sha256', $identity);
                $today      = now()->toDateString();

                $exists = DB::table('property_views')->where([
                    'property_id' => $property->id,
                    'viewer_hash' => $viewerHash,
                    'viewed_on'   => $today,
                ])->exists();

                DB::table('property_views')->updateOrInsert(
                    ['property_id' => $property->id, 'viewer_hash' => $viewerHash, 'viewed_on' => $today],
                    [
                        'user_id'    => $user->id ?? null,
                        'ip'         => $ip,
                        'session_id' => $sessionId,
                        'user_agent' => $ua,
                        'views'      => DB::raw('views + 1'),
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );

                if (!$exists) $property->increment('total_views');
            } else {
                $property->increment('total_views');
            }
        } catch (\Throwable $e) {
            $property->increment('total_views');
        }
    }

    // Normalize area range string to [mode, a, b]
    // mode: between|min|max|null
    private function normalizeAreaRange(string $range): array
    {
        if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) {
            return ['between', (int)$m[1], (int)$m[2]];
        }
        if (preg_match('/^(\d+)\+$/', $range, $m)) {
            return ['min', (int)$m[1], null];
        }
        if (preg_match('/^-(\d+)$/', $range, $m)) {
            return ['max', (int)$m[1], null];
        }
        return [null, null, null];
    }

    // Find type by slug or name (LIKE)
    private function getTypeByFlexibleInput(string $input, callable $escapeLike)
    {
        return Type::where('slug', $input)
            ->orWhere('name', 'like', '%'.$escapeLike($input).'%')
            ->first();
    }

    // Variants for a given purpose token (unchanged set)
    private function purposeVariants(string $p): array
    {
        return match ($p) {
            'sale'   => ['sale','buy','بيع'],
            'rent'   => ['rent','إيجار'],
            'wanted' => ['wanted','مطلوب'],
            default  => [$p],
        };
    }

    // Build the page title using the same priority rules from your code
    private function buildSearchPageTitle(
        ?string $purposeSlug,
        ?string $purposeParam,
        array   $purposeIn,
        ?string $typeSlug,
                $typeParam,
        callable $escapeLike,
        ?string $categorySlug,
        ?int    $categoryId,
        bool    $featuredOnly,
        ?string $sort
    ): string {
        // 1) Purpose text resolution
        $purposeText = null;
        if ($purposeSlug && isset(self::PURPOSE_TEXT[$purposeSlug])) {
            $purposeText = self::PURPOSE_TEXT[$purposeSlug];
        } elseif ($purposeParam && isset(self::PURPOSE_TEXT[$purposeParam])) {
            $purposeText = self::PURPOSE_TEXT[$purposeParam];
        } elseif (count($purposeIn) === 1) {
            $one = $purposeIn[0];
            $purposeText = self::PURPOSE_TEXT[$one] ?? null;
        }

        // 2) Type name (priority over category)
        $typeName = null;
        if ($typeSlug) {
            $typeName = Type::where('slug', $typeSlug)->value('name');
        } elseif ($typeParam !== null && $typeParam !== '') {
            if (is_numeric($typeParam)) {
                $typeName = Type::where('id', (int)$typeParam)->value('name');
            } else {
                $typeName = Type::where('slug', $typeParam)
                    ->orWhere('name', 'like', '%'.$escapeLike($typeParam).'%')
                    ->value('name');
            }
        }

        // 3) Category name if no type name
        $categoryName = null;
        if (!$typeName) {
            if ($categorySlug) {
                $categoryName = Type::whereNull('parent_id')->where('slug', $categorySlug)->value('name');
            } elseif ($categoryId) {
                $categoryName = Type::where('id', $categoryId)->whereNull('parent_id')->value('name');
            }
        }

        // 4) Priority: type > category > featured > purpose > newest > default
        if ($typeName) {
            return $purposeText ? ($typeName.' '.$purposeText) : $typeName;
        }
        if ($categoryName) {
            return $purposeText ? ($categoryName.' '.$purposeText) : $categoryName;
        }
        if ($featuredOnly) {
            return 'العقارات المميّزة';
        }
        if ($purposeText) {
            return 'عقارات '.$purposeText;
        }
        if (($sort ?? '') === 'newest') {
            return 'أحدث العقارات';
        }
        return 'نتائج البحث';
    }
}
