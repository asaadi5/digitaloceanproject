<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Mail\Websitemail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Http\Resources\PropertyResource;

use App\Models\{
    Location, Type, Package, Property, Agent, Wishlist, Testimonial, Post,
    Faq, Page, Subscriber, Order, User, Comment
};

class FrontController extends Controller
{
    /*────────────────────────────────────────────────────────────────────────────
    الدالة: index
    الغرض: نفس بيانات الصفحة الرئيسية ولكن كـ JSON للـ Flutter
    ────────────────────────────────────────────────────────────────────────────*/
    public function index()
    {
        // Featured strip (6)
        $properties = Property::publicVisible()->featured()->orderBy('id','asc')->take(6)->get();

        // Locations (14) + Top5 — نعتمد publicVisible لضمان تفعيل الوكيل
        $locations = Location::withCount(['properties' => fn ($q) => $q->publicVisible()])
            ->orderBy('properties_count', 'desc')->take(14)->get();

        $topLocations = Location::withCount(['properties' => fn($q) => $q->publicVisible()])
            ->orderBy('properties_count', 'desc')->take(5)->get();

        // Quick search facets
        $search_locations = Location::orderBy('name', 'asc')->get();
        $search_types     = Type::orderBy('name', 'asc')->get();

        // Agents (active) with public property counts
        $agents = Agent::where('status', 1)
            ->withCount(['properties' => fn ($q) => $q->publicVisible()])
            ->orderBy('id', 'asc')->take(7)->get();

        // Testimonials, posts
        $testimonials = Testimonial::orderBy('id', 'asc')->get();
        $posts        = Post::orderBy('id', 'desc')->take(4)->get();

        // Major type groups
        $landsTypeIds = $this->typeIdsFor(4);
        $recreTypeIds = $this->typeIdsFor(3);
        $commTypeIds  = $this->typeIdsFor(2);
        $resiTypeIds  = $this->typeIdsFor(1);

        $counts = [
            'lands'       => Property::publicVisible()->whereIn('type_id', $landsTypeIds)->count(),
            'recre'       => Property::publicVisible()->whereIn('type_id', $recreTypeIds)->count(),
            'commercial'  => Property::publicVisible()->whereIn('type_id', $commTypeIds)->count(),
            'residential' => Property::publicVisible()->whereIn('type_id', $resiTypeIds)->count(),
        ];

        $subtypes = Type::whereIn('parent_id', [1,2,3,4])
            ->withCount(['properties as properties_count' => fn ($q) => $q->publicVisible()])
            ->orderBy('parent_id')->orderBy('id')->get();

        // Featured & latest sliders (12)
        $featured_properties = Property::withBasicIncludes()
            ->publicVisible()->featured()
            ->withWishlistedCountFor()->latest()->take(12)->get();

        $latest_properties = Property::withBasicIncludes()
            ->publicVisible()
            ->withWishlistedCountFor()->latest()->take(12)->get();

        // KPIs
        $agents_total     = Agent::where('status', 1)->count();
        $orders_total     = Order::where('currently_active', 1)->where('status', 'Completed')->where('expire_date', '>=', now())->count();
        $properties_total = Property::publicVisible()->count();
        $users_total      = User::count();

        $latestPosts = Post::with('type')
            ->withCount(['comments as comments_count' => fn($q) => $q->where('approved', 1)])
            ->latest('id')->take(12)
            ->get(['id','title','slug','short_description','photo','type_id','total_views','created_at']);

        return response()->json([
            'strip_properties'    => PropertyResource::collection($properties),
            'locations'           => $locations,
            'top_locations'       => $topLocations,
            'search'              => [
                'locations' => $search_locations,
                'types'     => $search_types,
            ],
            'agents'              => $agents,
            'testimonials'        => $testimonials,
            'posts'               => $posts,
            'counts'              => $counts,
            'subtypes'            => $subtypes,
            'featured_properties' => PropertyResource::collection($featured_properties),
            'latest_properties'   => PropertyResource::collection($latest_properties),
            'totals'              => [
                'agents'     => $agents_total,
                'orders'     => $orders_total,
                'properties' => $properties_total,
                'users'      => $users_total,
            ],
            'latest_posts'        => $latestPosts,
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: blog
    ────────────────────────────────────────────────────────────────────────────*/
    public function blog(Request $request)
    {
        $postsQuery = Post::with('type')
            ->withCount(['comments as comments_count' => fn($q) => $q->where('approved', 1)])
            ->orderByDesc('id');

        if ($request->filled('type')) {
            $type = Type::where('slug', $request->type)->first();
            if ($type) $postsQuery->where('type_id', $type->id);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $postsQuery->where('title', 'like', "%{$q}%");
        }

        $posts = $postsQuery->paginate(9)->withQueryString();
        $types = Type::withCount('posts')->orderByDesc('posts_count')->get(['id','name','slug']);

        return response()->json([
            'types' => $types,
            'posts' => $posts,
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: post
    ────────────────────────────────────────────────────────────────────────────*/
    public function post($slug)
    {
        $post = Post::with([
            'type',
            'comments' => fn($q) => $q->where('approved', 1)->latest()
        ])
            ->withCount(['comments as comments_count' => fn($q) => $q->where('approved', 1)])
            ->where('slug', $slug)
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $post->increment('total_views');

        $types = Type::withCount('posts')->orderByDesc('posts_count')->get(['id','name','slug']);

        return response()->json([
            'post'  => $post,
            'types' => $types,
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: commentStore
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

        return response()->json(['message' => 'تم إرسال تعليقك بنجاح.']);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: faq / pricing
    ────────────────────────────────────────────────────────────────────────────*/
    public function faq()
    {
        $faqs = Faq::orderBy('id','asc')->get();
        return response()->json(['faqs' => $faqs]);
    }

    public function pricing()
    {
        $packages = Package::orderBy('id','asc')->get();
        return response()->json(['packages' => $packages]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: property_detail
    ────────────────────────────────────────────────────────────────────────────*/
    public function property_detail($slug)
    {
        // نفس علاقات الويب
        $property = Property::with([
            'agent:id,name,email,phone,city,address,photo',
            'location:id,name,slug',
            'type:id,name,slug,parent_id',
            'photos:id,property_id,photo',
            'videos:id,property_id,video',
            'documents:id,property_id,doc_type,issuer,doc_no,issued_at,file_path',
        ])->where('slug', $slug)->firstOrFail();

        // bump views (كما في الويب)
        $this->bumpPropertyViews($property);

        // أعلام النوع/الغرض (كما في الويب)
        $type     = optional($property->type);
        $parentId = (int) ($type->parent_id ?? 0);
        $isResi   = ($parentId === 1);
        $isCom    = ($parentId === 2);
        $isRecre  = ($parentId === 3);
        $isLand   = ($parentId === 4);
        $isRent   = \Illuminate\Support\Str::lower((string) $property->purpose) === 'rent';

        // History & rental rules (مطابق للويب)
        $priceHistory = \DB::table('property_price_history')
            ->where('property_id', $property->id)
            ->orderByDesc('effective_from')
            ->get();

        $rentalRules = \DB::table('property_rental_rules')
            ->where('property_id', $property->id)
            ->orderBy('id')
            ->get();

        // Amenities (مطابق للويب)
        $amenities = \DB::table('amenity_property')
            ->join('amenities', 'amenities.id', '=', 'amenity_property.amenity_id')
            ->where('amenity_property.property_id', $property->id)
            ->orderBy('amenities.name')
            ->pluck('amenities.name')
            ->toArray();

        // Related (مثل الويب: status = active)
        $related = Property::with(['type:id,name,parent_id', 'location:id,name'])
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->when($property->purpose, fn ($q) => $q->where('purpose', $property->purpose))
            ->when($property->type_id, fn ($q) => $q->where('type_id', $property->type_id))
            ->latest('id')->take(12)->get();

        // Agent latest (مثل الويب: status = active)
        $agentLatest = Property::with(['type:id,name', 'location:id,name'])
            ->where('agent_id', $property->agent_id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->latest('id')->take(6)->get();

        // Latest properties (مثل الويب تمامًا: active + 7 عناصر)
        $latestProperties = Property::where('status', 'active')
            ->latest('id')->take(7)->get();

        // 🔎 الخرائط: Flutter-friendly (lat/lng + maps_url)
        $mapInfo = $this->parseMapIframe($property->map);

        return response()->json([
            'property'          => new \App\Http\Resources\PropertyResource($property),
            'flags'             => compact('isResi','isCom','isRecre','isLand','isRent'),
            'price_history'     => $priceHistory,
            'rental_rules'      => $rentalRules,
            'amenities'         => $amenities,
            'related'           => \App\Http\Resources\PropertyResource::collection($related),
            'agent_latest'      => \App\Http\Resources\PropertyResource::collection($agentLatest),
            'latest_properties' => \App\Http\Resources\PropertyResource::collection($latestProperties),
            'map'               => $mapInfo, // 👈 نفس الترتيب اللي بدك ياه للفلاتر
        ]);
    }
    private function parseMapIframe(?string $iframeHtml): array
    {
        $lat = null; $lng = null; $src = null;

        if ($iframeHtml) {
            // استخرج src من iframe
            if (preg_match('~src=["\']([^"\']+)["\']~i', $iframeHtml, $m)) {
                $src = html_entity_decode(str_replace(['\"','\\/'], ['"','/'], $m[1]), ENT_QUOTES);
            }
            // استخرج lng/lat من الرابط
            if ($src && preg_match('~!2d([0-9.\-]+)!3d([0-9.\-]+)~', $src, $mm)) {
                $lng = $mm[1];
                $lat = $mm[2];
            }
        }

        // خليه يرجع lat/lng + رابط جاهز لفتح الخريطة
        return [
            'lat'      => $lat,
            'lng'      => $lng,
            'maps_url' => ($lat && $lng)
                ? "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}"
                : null,
        ];
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: property_send_message
    ────────────────────────────────────────────────────────────────────────────*/
    public function property_send_message(Request $request,$id)
    {
        $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['required','email','max:255'],
            'phone'   => ['nullable','string','max:255'],
            'message' => ['required','string'],
        ]);

        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $subject = 'Property Inquiry';
        $message = 'You have received a new inquiry for the property: ' . e($property->name).'<br><br>';
        $message .= 'Visitor Name:<br>'.e($request->name).'<br><br>';
        $message .= 'Visitor Email:<br>'.e($request->email).'<br><br>';
        if ($request->filled('phone')) {
            $message .= 'Visitor Phone:<br>'.e($request->phone).'<br><br>';
        }
        $message .= 'Visitor Message:<br>'.nl2br(e($request->message));

        $agent_email = optional($property->agent)->email;
        if ($agent_email) {
            Mail::to($agent_email)->send(new Websitemail($subject, $message));
        }

        return response()->json(['message' => 'Message sent successfully to agent']);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: locations
    ────────────────────────────────────────────────────────────────────────────*/
    public function locations()
    {
        // نحتسب فقط العقارات العامة المرئية (active + وكيل باقة فعّالة)
        $locations = Location::withCount(['properties' => fn($q) => $q->publicVisible()])
            ->orderBy('properties_count', 'desc')->paginate(20);

        return response()->json($locations);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: location
    ────────────────────────────────────────────────────────────────────────────*/
    public function location($slug)
    {
        $location = Location::where('slug', $slug)->first();
        if (!$location) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $properties = Property::publicVisible()
            ->where('location_id', $location->id)
            ->orderBy('id', 'asc')->paginate(6);

        return response()->json([
            'location'   => $location,
            'properties' => PropertyResource::collection($properties),
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: agents
    ────────────────────────────────────────────────────────────────────────────*/
    public function agents()
    {
        $agents = Agent::where('status', 1)->orderBy('id', 'asc')->paginate(20);
        return response()->json($agents);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: agent
    ────────────────────────────────────────────────────────────────────────────*/
    public function agent($id)
    {
        $agent = Agent::find($id);
        if (!$agent) {
            return response()->json(['message' => 'Agent not found'], 404);
        }

        $properties = Property::publicVisible()
            ->where('agent_id', $agent->id)
            ->orderBy('id', 'asc')->paginate(6);

        return response()->json([
            'agent'      => $agent,
            'properties' => PropertyResource::collection($properties),
        ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: property_search
    ────────────────────────────────────────────────────────────────────────────*/
    public function property_search(Request $request)
    {
        // Route-like slugs (optional from mobile)
        $purposeSlug  = $request->query('purpose_slug');   // sale|rent|wanted
        $categorySlug = $request->query('category_slug');  // residential|commercial|recreational|lands
        $typeSlug     = $request->query('type_slug');      // apartment|villa|...
        $locationSlug = $request->query('location_slug');  // if mobile sends slug
        $locationRow  = null;

        if ($locationSlug) {
            $locationRow = Location::select('id','name')->where('slug', $locationSlug)->first();
            if ($locationRow) $request->merge(['location_id' => $locationRow->id]);
        }

        if ($purposeSlug && !$request->filled('purpose_in')) {
            $request->merge(['purpose_in' => [$purposeSlug]]);
        }

        if ($categorySlug && !$request->filled('category_id')) {
            $catId = Type::whereNull('parent_id')->where('slug', $categorySlug)->value('id');
            if ($catId) $request->merge(['category_id' => $catId]);
        }

        if ($typeSlug && !$request->filled('type')) {
            $request->merge(['type' => $typeSlug]);
        }

        // Inputs
        $name          = trim((string) $request->query('name', ''));
        $typeParam     = $request->query('type');
        $areaRange     = trim((string) $request->query('area_range', ''));
        $cityText      = trim((string) $request->query('city_text', ''));
        $provinceText  = trim((string) $request->query('province_text', ''));
        $purposeParam  = trim((string) $request->query('purpose', ''));
        $categoryId    = $request->integer('category_id');
        $sort          = $request->query('sort', 'newest');

        $priceMin      = $request->query('price_min');
        $priceMax      = $request->query('price_max');
        $bedroomMin    = $request->query('bedroom');
        $featuredOnly  = $request->boolean('featured');
        $locationId    = $request->input('location_id');

        $escapeLike = static function (string $v): string {
            return addcslashes($v, "\\%_");
        };

        // نبدأ بعقارات نشِطة فقط
        $query = Property::query()->active();

        $purposeIn = (array) $request->query('purpose_in', []);
        if (!empty($purposeIn)) {
            $all = [];
            foreach ($purposeIn as $p) {
                $all = array_merge($all, $this->purposeVariants($p));
            }
            $query->whereIn('purpose', array_unique($all));
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        } elseif ($request->filled('city_text')) {
            $ct = trim((string) $request->query('city_text', ''));
            if ($ct !== '') $query->where('address', 'like', '%'.$escapeLike($ct).'%');
        }

        if ($categoryId) {
            $allowedTypeIds = Type::where('id', $categoryId)->orWhere('parent_id', $categoryId)->pluck('id');
            if ($allowedTypeIds->isNotEmpty()) $query->whereIn('type_id', $allowedTypeIds);
        }

        if ($typeParam !== null && $typeParam !== '') {
            if (is_numeric($typeParam)) {
                $query->where('type_id', (int) $typeParam);
            } else {
                $typeRow = $this->getTypeByFlexibleInput($typeParam, $escapeLike);
                if ($typeRow) $query->where('type_id', $typeRow->id);
            }
        }

        if ($name !== '') $query->where('name', 'like', '%'.$escapeLike($name).'%');

        if ($areaRange !== '') {
            $range = preg_replace('/\s+/', '', $areaRange);
            [$mode, $a, $b] = $this->normalizeAreaRange($range);
            if ($mode === 'between') {
                if ($a > $b) { [$a, $b] = [$b, $a]; }
                $query->whereBetween('size', [$a, $b]);
            } elseif ($mode === 'min') {
                $query->where('size', '>=', $a);
            } elseif ($mode === 'max') {
                $query->where('size', '<=', $a);
            }
        }

        if ($priceMin !== null && $priceMin !== '' && is_numeric($priceMin)) {
            $query->where('price', '>=', (float) $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '' && is_numeric($priceMax)) {
            $query->where('price', '<=', (float) $priceMax);
        }
        if ($bedroomMin !== null && $bedroomMin !== '' && is_numeric($bedroomMin)) {
            $query->where('bedroom', '>=', (int) $bedroomMin);
        }

        if ($cityText !== '')     $query->where('address', 'like', '%'.$escapeLike($cityText).'%');
        if ($provinceText !== '') $query->where('address', 'like', '%'.$escapeLike($provinceText).'%');

        if ($featuredOnly) $query->featured();

        // Apply exact same sort combos
        $this->applySort($query, $sort);

        // Paginated result + eager 'type'
        $properties = $query->with('type')->paginate(12)->withQueryString();

        // Side collections
        $resiTypeIds  = $this->typeIdsFor(1);
        $commTypeIds  = $this->typeIdsFor(2);
        $recreTypeIds = $this->typeIdsFor(3);
        $landsTypeIds = $this->typeIdsFor(4);

        // Page title
        $pageTitle = $this->buildSearchPageTitle(
            $purposeSlug, $purposeParam, $purposeIn,
            $typeSlug, $typeParam, $escapeLike,
            $categorySlug, $categoryId,
            $featuredOnly, $sort
        );

        return PropertyResource::collection($properties)
            ->additional([
                'pageTitle'    => $pageTitle,
                'resiTypeIds'  => $resiTypeIds,
                'commTypeIds'  => $commTypeIds,
                'recreTypeIds' => $recreTypeIds,
                'landsTypeIds' => $landsTypeIds,
            ]);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: wishlist_add
    ────────────────────────────────────────────────────────────────────────────*/
    public function wishlist_add($id)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $existing = Wishlist::where('user_id', $user->id)->where('property_id', $id)->first();
        if ($existing) return response()->json(['message' => 'Property already in wishlist'], 422);

        Wishlist::create([
            'user_id'     => $user->id,
            'property_id' => $id,
        ]);

        return response()->json(['message' => 'Property added to wishlist'], 201);
    }

    /*────────────────────────────────────────────────────────────────────────────
    الدالة: contact_submit / subscriber_send_email / subscriber_verify / terms / privacy
    ────────────────────────────────────────────────────────────────────────────*/
    public function contact_submit(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'    => ['required'],
            'email'   => ['required','email','unique:subscribers,email'],
            'message' => ['required'],
        ]);

        if(!$validator->passes()) {
            return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);
        }

        $subject = 'Contact Form Message';
        $message = 'Sender Information:<br>';
        $message .= '<b>Name:</b><br>'.e($request->name).'<br><br>';
        $message .= '<b>Email:</b><br>'.e($request->email).'<br><br>';
        $message .= '<b>Message:</b><br>'.nl2br(e($request->message));

        Mail::to($request->email)->send(new Websitemail($subject,$message));

        return response()->json(['code'=>1,'success_message'=>'Message is sent successfully']);
    }

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
        $obj->email  = $request->email;
        $obj->token  = $token;
        $obj->status = 0;
        $obj->save();

        $verification_link = url('subscriber/verify/'.$request->email.'/'.$token);

        $subject = 'Subscriber Verification';
        $message = 'Please click on the link below to confirm subscription:<br>';
        $message .= '<a href="'.$verification_link.'">'.$verification_link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject,$message));

        return response()->json(['code'=>1,'success_message'=>'Please check your email to confirm subscription']);
    }

    public function subscriber_verify($email,$token)
    {
        $subscriber_data = Subscriber::where('email',$email)->where('token',$token)->first();

        if($subscriber_data) {
            $subscriber_data->token  = '';
            $subscriber_data->status = 1;
            $subscriber_data->update();

            return response()->json(['message' => 'Your subscription is verified successfully!']);
        }

        return response()->json(['message' => 'Invalid verification link'], 404);
    }

    public function terms()
    {
        $terms_data = Page::where('id',1)->first();
        return response()->json(['terms' => $terms_data]);
    }

    public function privacy()
    {
        $privacy_data = Page::where('id',1)->first();
        return response()->json(['privacy' => $privacy_data]);
    }

    /*──────────────────────── Helpers ───────────────────────*/

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

    private function typeIdsFor(int $parentId)
    {
        return Type::where('id', $parentId)->orWhere('parent_id', $parentId)->pluck('id');
    }

    private function applySort(\Illuminate\Database\Eloquent\Builder $q, ?string $sort): void
    {
        $plan = self::SORT_MAP[$sort] ?? self::SORT_MAP['newest'];
        foreach ($plan as [$col, $dir]) $q->orderBy($col, $dir);
    }

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
                        'created_at' => now(),
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

    private function normalizeAreaRange(string $range): array
    {
        if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) return ['between', (int)$m[1], (int)$m[2]];
        if (preg_match('/^(\d+)\+$/', $range, $m))    return ['min', (int)$m[1], null];
        if (preg_match('/^-(\d+)$/', $range, $m))     return ['max', (int)$m[1], null];
        return [null, null, null];
    }

    private function getTypeByFlexibleInput(string $input, callable $escapeLike)
    {
        return Type::where('slug', $input)
            ->orWhere('name', 'like', '%'.$escapeLike($input).'%')
            ->first();
    }

    private function purposeVariants(string $p): array
    {
        return match ($p) {
            'sale'   => ['sale','buy','بيع'],
            'rent'   => ['rent','إيجار'],
            'wanted' => ['wanted','مطلوب'],
            default  => [$p],
        };
    }

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
        $purposeText = null;
        if ($purposeSlug && isset(self::PURPOSE_TEXT[$purposeSlug])) {
            $purposeText = self::PURPOSE_TEXT[$purposeSlug];
        } elseif ($purposeParam && isset(self::PURPOSE_TEXT[$purposeParam])) {
            $purposeText = self::PURPOSE_TEXT[$purposeParam];
        } elseif (count($purposeIn) === 1) {
            $one = $purposeIn[0];
            $purposeText = self::PURPOSE_TEXT[$one] ?? null;
        }

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

        $categoryName = null;
        if (!$typeName) {
            if ($categorySlug) {
                $categoryName = Type::whereNull('parent_id')->where('slug', $categorySlug)->value('name');
            } elseif ($categoryId) {
                $categoryName = Type::where('id', $categoryId)->whereNull('parent_id')->value('name');
            }
        }

        if ($typeName)                 return $purposeText ? ($typeName.' '.$purposeText) : $typeName;
        if ($categoryName)             return $purposeText ? ($categoryName.' '.$purposeText) : $categoryName;
        if ($featuredOnly)             return 'العقارات المميّزة';
        if ($purposeText)              return 'عقارات '.$purposeText;
        if (($sort ?? '') === 'newest') return 'أحدث العقارات';
        return 'نتائج البحث';
    }
}
