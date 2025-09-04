<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Mail};
use App\Mail\Websitemail;
use App\Models\{Agent, Package, Order, Admin, Property, Location, Type, Amenity, PropertyPhoto, PropertyVideo, Message, MessageReply, User};
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AgentController extends Controller
{
    // ========== Auth ==========

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

        Mail::to($request->email)->send(new Websitemail($subject,$message));

        return response()->json(['message' => 'Registered. Verify your email.'], 201);
    }

    // GET /api/v1/agent/auth/verify/{token}/{email}
    public function verify($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if (!$agent) return response()->json(['message' => 'Invalid token or email'], 404);

        $agent->token = '';
        $agent->status = 1;
        $agent->save();

        return response()->json(['message' => 'Account verified. You can login now.']);
    }

    // POST /api/v1/agent/auth/login
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required', // email or username
            'password' => 'required',
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $agent = Agent::where($field, $login)->where('status',1)->first();
        if (!$agent || !Hash::check($request->password, $agent->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        // Ensure Agent model uses HasApiTokens
        $token = $agent->createToken('agent-mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'agent' => [
                'id'       => $agent->id,
                'name'     => $agent->name,
                'username' => $agent->username,
                'email'    => $agent->email,
                'company'  => $agent->company,
                'phone'    => $agent->phone,
                'photo'    => $agent->photo ? url('uploads/'.$agent->photo) : null,
            ],
        ]);
    }

    // POST /api/v1/agent/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // ========== Profile & Dashboard ==========

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
            'name'   => 'required',
            'email'  => 'required|email|unique:agents,email,'.$a->id,
            'company'=> 'required',
        ]);

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
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
            'active'   => Property::where('agent_id', $aid)->where('status', 'Active')->count(),
            'pending'  => Property::where('agent_id', $aid)->where('status', 'Pending')->count(),
            'featured' => Property::where('agent_id', $aid)->where('status', 'Active')->where('is_featured', 'Yes')->count(),
        ];

        $recent = Property::where('agent_id', $aid)->where('status','Active')->orderBy('id','desc')->take(5)->get();

        return response()->json(['counts' => $counts, 'recent' => $recent]);
    }

    // ========== Payments (Bootstrap links for mobile webview) ==========

    // POST /api/v1/agent/payments/paypal
    public function paypalCreate(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = Package::find($request->package_id);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('agent_paypal_success'),
                "cancel_url" => route('agent_paypal_cancel')
            ],
            "purchase_units" => [[
                "amount" => ["currency_code" => "USD", "value" => $package->price]
            ]]
        ]);

        if(isset($response['id']) && $response['id']){
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
        $package = Package::find($request->package_id);

        $stripe   = new \Stripe\StripeClient(config('stripe.stripe_sk'));
        $checkout = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $package->name],
                    'unit_amount'  => $package->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('agent_stripe_success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('agent_stripe_cancel'),
        ]);

        return response()->json([
            'session_id' => $checkout->id ?? null,
            'checkout_url' => $checkout->url ?? null
        ]);
    }

    // ========== Properties ==========

    // GET /api/v1/agent/properties
    public function properties(Request $request)
    {
        $list = Property::where('agent_id', $request->user()->id)->latest('id')->get();
        return response()->json(['properties' => $list]);
    }

    // POST /api/v1/agent/properties
    public function propertyStore(Request $request)
    {
        $request->validate([
            'name'           => ['required'],
            'slug'           => ['required','unique:properties,slug', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'          => ['required'],
            'size'           => ['required', 'numeric'],
            'bedroom'        => ['required', 'numeric'],
            'bathroom'       => ['required', 'numeric'],
            'address'        => ['required'],
            'featured_photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
        ]);

        $final = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
        $request->featured_photo->move(public_path('uploads'), $final);

        $prop = new Property();
        $prop->agent_id       = $request->user()->id;
        $prop->location_id    = $request->location_id;
        $prop->type_id        = $request->type_id;
        $prop->amenities      = is_array($request->amenity) ? implode(',', $request->amenity) : null;
        $prop->name           = $request->name;
        $prop->slug           = $request->slug;
        $prop->description    = $request->description;
        $prop->price          = $request->price;
        $prop->featured_photo = $final;
        $prop->purpose        = $request->purpose;
        $prop->bedroom        = $request->bedroom;
        $prop->bathroom       = $request->bathroom;
        $prop->size           = $request->size;
        $prop->floor          = $request->floor;
        $prop->garage         = $request->garage;
        $prop->balcony        = $request->balcony;
        $prop->address        = $request->address;
        $prop->built_year     = $request->built_year;
        $prop->map            = $request->map;
        $prop->is_featured    = $request->is_featured;
        $prop->status         = 'Pending';
        $prop->save();

        return response()->json(['message' => 'Created', 'id' => $prop->id], 201);
    }

    // POST /api/v1/agent/properties/{id}
    public function propertyUpdate(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $request->validate([
            'name'     => ['required'],
            'slug'     => ['required','unique:properties,slug,'.$p->id, 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'    => ['required'],
            'size'     => ['required', 'numeric'],
            'bedroom'  => ['required', 'numeric'],
            'bathroom' => ['required', 'numeric'],
            'address'  => ['required'],
        ]);

        if ($request->hasFile('featured_photo')) {
            $request->validate(['featured_photo' => ['image','mimes:jpeg,png,jpg,gif,svg','max:2048']]);
            $final = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
            if ($p->featured_photo && file_exists(public_path('uploads/'.$p->featured_photo))) {
                @unlink(public_path('uploads/'.$p->featured_photo));
            }
            $request->featured_photo->move(public_path('uploads'), $final);
            $p->featured_photo = $final;
        }

        $p->location_id = $request->location_id;
        $p->type_id     = $request->type_id;
        $p->amenities   = is_array($request->amenity) ? implode(',', $request->amenity) : null;
        $p->name        = $request->name;
        $p->slug        = $request->slug;
        $p->description = $request->description;
        $p->price       = $request->price;
        $p->purpose     = $request->purpose;
        $p->bedroom     = $request->bedroom;
        $p->bathroom    = $request->bathroom;
        $p->size        = $request->size;
        $p->floor       = $request->floor;
        $p->garage      = $request->garage;
        $p->balcony     = $request->balcony;
        $p->address     = $request->address;
        $p->built_year  = $request->built_year;
        $p->map         = $request->map;
        $p->is_featured = $request->is_featured;
        $p->save();

        return response()->json(['message' => 'Updated']);
    }

    // DELETE /api/v1/agent/properties/{id}
    public function propertyDelete(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        if ($p->featured_photo && file_exists(public_path('uploads/'.$p->featured_photo))) {
            @unlink(public_path('uploads/'.$p->featured_photo));
        }
        $photos = PropertyPhoto::where('property_id',$p->id)->get();
        foreach($photos as $ph){
            if ($ph->photo && file_exists(public_path('uploads/'.$ph->photo))) {
                @unlink(public_path('uploads/'.$ph->photo));
            }
        }
        $p->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // ========== Gallery ==========

    // GET /api/v1/agent/properties/{id}/photos
    public function photos(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $list = PropertyPhoto::where('property_id',$id)->latest('id')->get();
        return response()->json(['photos' => $list]);
    }

    // POST /api/v1/agent/properties/{id}/photos
    public function photoStore(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $request->validate(['photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048']]);

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

        if ($ph->photo && file_exists(public_path('uploads/'.$ph->photo))) {
            @unlink(public_path('uploads/'.$ph->photo));
        }
        $ph->delete();

        return response()->json(['message' => 'Photo deleted']);
    }

    // GET /api/v1/agent/properties/{id}/videos
    public function videos(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $list = PropertyVideo::where('property_id',$id)->latest('id')->get();
        return response()->json(['videos' => $list]);
    }

    // POST /api/v1/agent/properties/{id}/videos
    public function videoStore(Request $request, $id)
    {
        $p = Property::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$p) return response()->json(['message' => 'Not found'], 404);

        $request->validate(['video' => ['required']]);

        $v = new PropertyVideo();
        $v->property_id = $p->id;
        $v->video       = $request->video;
        $v->save();

        return response()->json(['message' => 'Video added', 'id' => $v->id], 201);
    }

    // DELETE /api/v1/agent/videos/{video_id}
    public function videoDelete(Request $request, $video_id)
    {
        $v = PropertyVideo::find($video_id);
        if (!$v) return response()->json(['message' => 'Not found'], 404);

        $v->delete();
        return response()->json(['message' => 'Video deleted']);
    }

    // ========== Messages ==========

    // GET /api/v1/agent/messages
    public function messages(Request $request)
    {
        $list = Message::where('agent_id', $request->user()->id)->latest('id')->get();
        return response()->json(['messages' => $list]);
    }

    // GET /api/v1/agent/messages/{id}
    public function messageShow(Request $request, $id)
    {
        $m = Message::where('id',$id)->where('agent_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        $replies = MessageReply::where('message_id',$id)->latest('id')->get();
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

        $subject = 'New Reply from Agent';
        $body    = 'You have received a new reply from agent. Please click on the following link:<br>';
        $link    = url('message/reply/'.$m->id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        $user = User::find($request->user_id);
        if ($user) {
            Mail::to($user->email)->send(new Websitemail($subject, $body));
        }

        return response()->json(['message' => 'Reply sent']);
    }
}
