<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Mail};
use App\Mail\Websitemail;
use App\Models\{
    Agent, Package, Order, Property, Location, Type, Amenity,
    PropertyPhoto, PropertyVideo, Message, MessageReply, User
};
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AgentController extends Controller
{
    /* ========================= Auth ========================= */

    // POST /api/v1/agent/auth/register
    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'username'         => 'required|string|max:255|unique:agents,username',
            'email'            => 'required|max:255|email|unique:agents,email',
            'company'          => 'required|string|max:255',
            'phone'            => 'required|string|max:255',
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $token = hash('sha256', time());

        $agent = new Agent();
        $agent->name     = $request->name;
        $agent->username = $request->username;
        $agent->email    = $request->email;
        $agent->company  = $request->company;
        $agent->phone    = $request->phone;
        $agent->password = Hash::make($request->password);
        $agent->token    = $token;
        $agent->save();

        $link    = url('agent/registration-verify/'.$token.'/'.$request->email);
        $subject = 'Registration Verification';
        $message = 'Click the link to verify your account:<br><a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return response()->json(['message' => 'Registered. Verify your email.'], 201);
    }

    // GET /api/v1/agent/auth/verify/{token}/{email}
    public function verify($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if (!$agent) return response()->json(['message' => 'Invalid token or email'], 404);

        $agent->token  = '';
        $agent->status = 1;
        $agent->save();

        return response()->json(['message' => 'Account verified. You can login now.']);
    }

    // POST /api/v1/agent/auth/login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('agent')->attempt([$fieldType => $request->email, 'password' => $request->password, 'status' => 1])) {
            $agent = Auth::guard('agent')->user();

            // ⬇️ إنشاء توكين مع صلاحية "agent"
            $token = $agent->createToken('flutter-agent', ['agent'])->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token'  => $token,
                'agent'  => $agent,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'بيانات الدخول غير صحيحة'], 401);
    }


    // POST /api/v1/agent/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    /* ================== Profile & Dashboard ================= */

    // GET /api/v1/agent/me
    public function me(Request $request)
    {
        $a = $request->user();
        return response()->json([
            'id'       => $a->id,
            'name'     => $a->name,
            'username' => $a->username,
            'email'    => $a->email,
            'company'  => $a->company,
            'phone'    => $a->phone,
            'photo'    => $a->photo ? url('uploads/'.$a->photo) : null,
        ]);
    }

    // POST /api/v1/agent/profile
    public function updateProfile(Request $request)
    {
        $a = $request->user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:agents,email,'.$a->id,
            'company' => 'required|string|max:255',
            'photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $final = 'agent_'.time().'.'.$request->photo->extension();
            if ($a->photo && file_exists(public_path('uploads/'.$a->photo))) {
                @unlink(public_path('uploads/'.$a->photo));
            }
            $request->photo->move(public_path('uploads'), $final);
            $a->photo = $final;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password'         => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $a->password = Hash::make($request->password);
        }

        $a->name        = $request->name;
        $a->email       = $request->email;
        $a->company     = $request->company;
        $a->designation = $request->designation;
        $a->phone       = $request->phone;
        $a->address     = $request->address;
        $a->country     = $request->country;
        $a->state       = $request->state;
        $a->city        = $request->city;
        $a->zip         = $request->zip;
        $a->facebook    = $request->facebook;
        $a->twitter     = $request->twitter;
        $a->linkedin    = $request->linkedin;
        $a->instagram   = $request->instagram;
        $a->website     = $request->website;
        $a->biography   = $request->biography;
        $a->save();

        return response()->json(['message' => 'Profile updated']);
    }

    // GET /api/v1/agent/dashboard
    public function dashboard(Request $request)
    {
        $aid = $request->user()->id;

        $counts = [
            'active'   => Property::where('agent_id', $aid)->where('status', 'active')->count(),
            'pending'  => Property::where('agent_id', $aid)->where('status', 'Pending')->count(),
            'featured' => Property::where('agent_id', $aid)->where('status', 'active')->where('is_featured', 'Yes')->count(),
        ];

        $recent = Property::where('agent_id', $aid)->where('status','active')->orderBy('id','desc')->take(5)->get();

        return response()->json(['counts' => $counts, 'recent' => $recent]);
    }

    /* ============ Payments (Bootstrap links for mobile) ============ */

    // POST /api/v1/agent/payments/paypal
    public function paypalCreate(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = Package::findOrFail($request->package_id);
        $agentId = $request->user()->id;

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('agent_paypal_success'), // الويب سيقرأ custom_id كـ fallback
                "cancel_url" => route('agent_paypal_cancel')
            ],
            "purchase_units" => [[
                "amount"     => ["currency_code" => "USD", "value" => (string)$package->price],
                "custom_id"  => $agentId.'|'.$package->id, // ✅ حتى لو ما في session
            ]]
        ]);

        if (isset($response['id']) && $response['id']) {
            $approve = collect($response['links'] ?? [])->firstWhere('rel','approve');
            return response()->json([
                'order_id'    => $response['id'],
                'approve_url' => $approve['href'] ?? null
            ]);
        }

        return response()->json(['message' => 'Unable to create PayPal order'], 500);
    }

    // POST /api/v1/agent/payments/stripe
    public function stripeCreate(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = Package::findOrFail($request->package_id);
        $agentId = $request->user()->id;

        $stripe   = new \Stripe\StripeClient(config('stripe.stripe_sk'));
        $checkout = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $package->name],
                    'unit_amount'  => (int)round($package->price * 100),
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('agent_stripe_success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('agent_stripe_cancel'),
            'metadata'    => [
                'agent_id'   => (string)$agentId,
                'package_id' => (string)$package->id,
            ],
        ]);

        return response()->json([
            'session_id'   => $checkout->id ?? null,
            'checkout_url' => $checkout->url ?? null
        ]);
    }

    /* ========================= Properties ========================= */

    // GET /api/v1/agent/properties
    public function properties(Request $request)
    {
        $list = Property::where('agent_id', $request->user()->id)->latest('id')->get();
        return response()->json(['properties' => $list]);
    }

    // POST /api/v1/agent/properties
    public function propertyStore(Request $request)
    {
        $aid = $request->user()->id;

        // تحقق الباقة بنفس منطق الويب
        if ($msg = $this->assertCanCreateProperty($aid)) {
            return response()->json(['message' => $msg], 403);
        }

        $request->validate([
            'name'           => ['required'],
            'slug'           => ['required','unique:properties,slug','regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'          => ['required'],
            'size'           => ['required','numeric'],
            'bedroom'        => ['required','numeric'],
            'bathroom'       => ['required','numeric'],
            'address'        => ['required'],
            'purpose'        => ['required','in:buy,rent'],
            'location_id'    => ['required','exists:locations,id'],
            'type_id'        => ['required','exists:types,id'],
            'featured_photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:4096'],
            'gallery_photos.*'=>['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:4096'],
            'video_url'      => ['nullable','string','max:255'],
        ]);

        DB::beginTransaction();
        try {
            $uploadDir = public_path('uploads');
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

            $final = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
            $request->featured_photo->move($uploadDir, $final);

            $p = new Property();
            $p->agent_id       = $aid;
            $p->location_id    = $request->location_id;
            $p->type_id        = $request->type_id;
            $p->name           = $request->name;
            $p->slug           = $request->slug;
            $p->description    = $request->description;
            $p->price          = $request->price;
            $p->featured_photo = $final;
            $p->purpose        = $request->purpose; // buy|rent
            $p->bedroom        = $request->bedroom;
            $p->bathroom       = $request->bathroom;
            $p->size           = $request->size;
            $p->floor          = $request->floor;
            $p->garage         = $request->garage;
            $p->balcony        = $request->balcony;
            $p->address        = $request->address;
            $p->built_year     = $request->built_year;
            $p->map            = $this->sanitizeMap($request->map);
            $p->is_featured    = $request->is_featured;
            $p->status         = 'Pending';
            $p->area           = $request->area;
            $p->save();

            // amenities pivot
            $amenities = array_map('intval', (array)$request->input('amenity', []));
            $p->amenities()->sync($amenities);

            // gallery (optional)
            if ($request->hasFile('gallery_photos')) {
                $i = 0;
                foreach ($request->file('gallery_photos') as $file) {
                    if (!$file->isValid()) continue;
                    $gn = 'property_photo_'.time().'_'.$i.'.'.$file->extension();
                    $file->move($uploadDir, $gn);
                    PropertyPhoto::create(['property_id'=>$p->id,'photo'=>$gn]);
                    $i++;
                }
            }

            // video (optional) — store YouTube ID only
            if ($request->filled('video_url')) {
                $yt = $this->extractYouTubeId($request->video_url);
                if (!$yt) {
                    throw new \RuntimeException('Please enter a valid YouTube URL or ID.');
                }
                PropertyVideo::create(['property_id'=>$p->id,'video'=>$yt]);
            }

            DB::commit();
            return response()->json(['message' => 'Created','id'=>$p->id], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($final) && file_exists(public_path('uploads/'.$final))) {
                @unlink(public_path('uploads/'.$final));
            }
            return response()->json(['message' => 'Failed to create property: '.$e->getMessage()], 422);
        }
    }

    // POST /api/v1/agent/properties/{id}
    public function propertyUpdate(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $request->validate([
            'name'     => ['required'],
            'slug'     => ['required','unique:properties,slug,'.$p->id,'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'    => ['required'],
            'size'     => ['required','numeric'],
            'bedroom'  => ['required','numeric'],
            'bathroom' => ['required','numeric'],
            'address'  => ['required'],
            'featured_photo' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:4096'],
            'video_url'=> ['nullable','string','max:255'],
        ]);

        // unify purpose like web
        $incomingPurpose = strtolower((string)$request->input('purpose', $p->purpose));
        if ($incomingPurpose === 'sale') $incomingPurpose = 'buy';
        if (!in_array($incomingPurpose, ['buy','rent'], true)) {
            $incomingPurpose = $p->purpose;
        }

        DB::beginTransaction();
        try {
            $uploadDir = public_path('uploads');
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

            if ($request->hasFile('featured_photo')) {
                $final = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
                if ($p->featured_photo && file_exists($uploadDir.'/'.$p->featured_photo)) {
                    @unlink($uploadDir.'/'.$p->featured_photo);
                }
                $request->featured_photo->move($uploadDir, $final);
                $p->featured_photo = $final;
            }

            $p->location_id = $request->location_id ?? $p->location_id;
            $p->type_id     = $request->type_id ?? $p->type_id;
            $p->name        = $request->name;
            $p->slug        = $request->slug;
            $p->description = $request->description;
            $p->price       = $request->price;
            $p->purpose     = $incomingPurpose;
            $p->bedroom     = $request->bedroom;
            $p->bathroom    = $request->bathroom;
            $p->size        = $request->size;
            $p->floor       = $request->floor;
            $p->garage      = $request->garage;
            $p->balcony     = $request->balcony;
            $p->address     = $request->address;
            $p->built_year  = $request->built_year;
            $p->map         = $request->has('map') ? $this->sanitizeMap($request->map) : $p->map;
            $p->is_featured = $request->is_featured ?? $p->is_featured;
            $p->area        = $request->area ?? $p->area;
            $p->save();

            // amenities pivot
            $amenities = array_map('intval', (array)$request->input('amenity', []));
            $p->amenities()->sync($amenities);

            // extra video (optional)
            if ($request->filled('video_url')) {
                $yt = $this->extractYouTubeId($request->video_url);
                if (!$yt) throw new \RuntimeException('Please enter a valid YouTube URL or ID.');
                PropertyVideo::create(['property_id'=>$p->id,'video'=>$yt]);
            }

            // extra gallery photos (optional)
            if ($request->hasFile('gallery_photos')) {
                foreach ($request->file('gallery_photos') as $file) {
                    if (!$file->isValid()) continue;
                    $name = 'property_photo_'.time().'_'.uniqid().'.'.$file->extension();
                    $file->move($uploadDir, $name);
                    PropertyPhoto::create(['property_id'=>$p->id,'photo'=>$name]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Updated']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update property: '.$e->getMessage()], 422);
        }
    }

    // DELETE /api/v1/agent/properties/{id}
    public function propertyDelete(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $uploadDir = public_path('uploads');

        if ($p->featured_photo && file_exists($uploadDir.'/'.$p->featured_photo)) {
            @unlink($uploadDir.'/'.$p->featured_photo);
        }

        $photos = PropertyPhoto::where('property_id',$p->id)->get();
        foreach ($photos as $ph) {
            if ($ph->photo && file_exists($uploadDir.'/'.$ph->photo)) {
                @unlink($uploadDir.'/'.$ph->photo);
            }
        }
        PropertyPhoto::where('property_id',$p->id)->delete();
        PropertyVideo::where('property_id',$p->id)->delete();

        $p->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /* ============================ Gallery ============================ */

    // GET /api/v1/agent/properties/{id}/photos
    public function photos(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $list = PropertyPhoto::where('property_id',$id)->latest('id')->get();
        return response()->json(['photos' => $list]);
    }

    // POST /api/v1/agent/properties/{id}/photos
    public function photoStore(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $request->validate(['photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:4096']]);

        $final = 'property_photo_'.time().'.'.$request->photo->extension();
        $request->photo->move(public_path('uploads'), $final);

        $ph = new PropertyPhoto();
        $ph->property_id = $p->id;
        $ph->photo       = $final;
        $ph->save();

        return response()->json(['message' => 'Photo added', 'id' => $ph->id], 201);
    }

    // DELETE /api/v1/agent/photos/{photo_id}
    public function photoDelete(Request $request, $photo_id)
    {
        $ph = PropertyPhoto::find($photo_id);
        if (!$ph) return response()->json(['message' => 'Not found'], 404);

        // تأكد الصورة لعقار يملكه الوكيل
        $p = Property::where('id',$ph->property_id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not authorized'], 403);

        $path = public_path('uploads/'.$ph->photo);
        if (is_file($path)) @unlink($path);

        $ph->delete();

        return response()->json(['message' => 'Photo deleted']);
    }

    // GET /api/v1/agent/properties/{id}/videos
    public function videos(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $list = PropertyVideo::where('property_id',$id)->latest('id')->get();
        return response()->json(['videos' => $list]);
    }

    // POST /api/v1/agent/properties/{id}/videos
    public function videoStore(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $request->validate(['video' => ['required']]);

        $ytId = $this->extractYouTubeId($request->video);
        if (!$ytId) return response()->json(['message' => 'Please enter a valid YouTube URL or ID.'], 422);

        $v = new PropertyVideo();
        $v->property_id = $p->id;
        $v->video       = $ytId;
        $v->save();

        return response()->json(['message' => 'Video added', 'id' => $v->id], 201);
    }

    // DELETE /api/v1/agent/videos/{video_id}
    public function videoDelete(Request $request, $video_id)
    {
        $v = PropertyVideo::find($video_id);
        if (!$v) return response()->json(['message' => 'Not found'], 404);

        // تحقق الملكية
        $p = Property::where('id',$v->property_id)->where('agent_id',$request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not authorized'], 403);

        $v->delete();
        return response()->json(['message' => 'Video deleted']);
    }

    /* ============================ Messages ============================ */

    // GET /api/v1/agent/messages
    public function messages(Request $request)
    {
        $list = Message::with('user:id,name,email,photo')
            ->where('agent_id', $request->user()->id)
            ->latest('id')->get();

        return response()->json(['messages' => $list]);
    }

    // GET /api/v1/agent/messages/{id}
    public function messageShow(Request $request, $id)
    {
        $m = Message::with('user:id,name,email,photo')
            ->where('id',$id)->where('agent_id',$request->user()->id)->first();

        if (!$m) return response()->json(['message' => 'Not found'], 404);

        $replies = MessageReply::with(['user:id,name,photo','agent:id,name,photo'])
            ->where('message_id',$id)->orderBy('id')->get();

        return response()->json(['message' => $m, 'replies' => $replies]);
    }

    // POST /api/v1/agent/messages/{id}/reply
    public function messageReply(Request $request, $id)
    {
        $request->validate(['reply' => 'required', 'user_id' => 'required|exists:users,id']);

        $m = Message::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        $r = new MessageReply();
        $r->message_id = $m->id;
        $r->user_id    = $request->user_id;
        $r->agent_id   = $request->user()->id;
        $r->sender     = 'Agent';
        $r->reply      = $request->reply;
        $r->save();

        // بريد للعميل (نفس رابط الويب)
        $subject = 'New Reply from Agent';
        $body    = 'You have received a new reply from agent. Please click on the following link:<br>';
        $link    = route('message_reply', $m->id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        if ($user = User::find($request->user_id)) {
            Mail::to($user->email)->send(new Websitemail($subject, $body));
        }

        return response()->json(['message' => 'Reply sent']);
    }

    /* ============================ Helpers ============================ */

    private function extractYouTubeId(string $input): ?string
    {
        $s = trim($input);
        if (preg_match('~^[A-Za-z0-9_-]{8,64}$~', $s)) return $s;
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i', $s, $m)) {
            return $m[1];
        }
        return null;
    }

    private function sanitizeMap(?string $html): ?string
    {
        if (!$html) return null;
        if (preg_match('~<iframe[^>]*\s+src="([^"]+)"[^>]*></iframe>~i', $html, $m)) {
            $src = $m[1];
            if (!preg_match('~^(https:)?//(www\.)?(google\.com|maps\.googleapis\.com|www\.openstreetmap\.org)/~i', $src)) {
                return null;
            }
            return '<iframe src="'.$src.'" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
        }
        return null;
    }

    // نفس تحققات الويب قبل إنشاء عقار
    private function assertCanCreateProperty(int $agentId): ?string
    {
        $order = Order::where('agent_id', $agentId)->where('currently_active', 1)->first();
        if (!$order) {
            return 'You have not purchased any package yet. Please purchase a package to create properties.';
        }
        if ($order->expire_date < date('Y-m-d')) {
            return 'Your package has expired. Please purchase a new package to create properties.';
        }
        $owned = Property::where('agent_id', $agentId)->count();
        if ($order->package->allowed_properties <= $owned) {
            return 'You have reached the maximum number of properties allowed in your package.';
        }
        return null;
    }
}
