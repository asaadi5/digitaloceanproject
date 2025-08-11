<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Mail\Websitemail;
use Auth;

class FrontController extends Controller
{
    public function index()
    {
        $properties = Property::where('status', 'Active')->where('is_featured','Yes')
            ->whereHas('agent', function($query) {
                $query->whereHas('orders', function($q) {
                    $q->where('currently_active', 1)
                        ->where('status', 'Completed')
                        ->where('expire_date', '>=', now());
                });
            })
        ->orderBy('id', 'asc')
        ->take(3)
        ->get();

        // get the total propertywise locations. which location has more property, that will come first and in this way.
        $locations = Location::withCount(['properties' => function ($query) {
            $query->where('status', 'Active')
            ->whereHas('agent', function($q) {
                $q->whereHas('orders', function($qq) {
                    $qq->where('currently_active', 1)
                        ->where('status', 'Completed')
                        ->where('expire_date', '>=', now());
                });
            });
        }])->orderBy('properties_count', 'desc')->take(4)->get();

        $search_locations = Location::orderBy('name', 'asc')->get();
        $search_types = Type::orderBy('name', 'asc')->get();
        $agents = Agent::where('status', 1)->orderBy('id', 'asc')->take(4)->get();
        $testimonials = Testimonial::orderBy('id','asc')->get();
        $posts = Post::orderBy('id','desc')->take(3)->get();
        
        return view('front.home', compact('properties', 'locations', 'agents', 'search_locations', 'search_types', 'testimonials', 'posts'));
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function contact_submit(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name' => ['required'],
            'email' => ['required','email','unique:subscribers,email'],
            'message' => ['required'],
        ]);

        if(!$validator->passes()) {
            return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);
        } else {
            
            // Send email
            $subject = 'Contact Form Message';
            $message = 'Sender Information:<br>';
            $message .= '<b>Name:</b><br>'.$request->name.'<br><br>';
            $message .= '<b>Email:</b><br>'.$request->email.'<br><br>';
            $message .= '<b>Message:</b><br>'.nl2br($request->message);

            \Mail::to($request->email)->send(new Websitemail($subject,$message));

            return response()->json(['code'=>1,'success_message'=>'Message is sent successfully']);
        }
    }

    public function blog()
    {
        $posts = Post::orderBy('id','desc')->paginate(15);
        return view('front.blog', compact('posts'));
    }

    public function post($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if (!$post) {
            return redirect()->route('blog')->with('error', 'Post not found');
        }

        $new_view = $post->total_views + 1;
        $post->total_views = $new_view;
        $post->save();

        return view('front.post', compact('post'));
    }

    public function faq()
    {
        $faqs = Faq::orderBy('id','asc')->get();
        return view('front.faq', compact('faqs'));
    }

    public function select_user()
    {
        return view('front.select_user');
    }

    public function pricing()
    {
        $packages = Package::orderBy('id','asc')->get();
        return view('front.pricing', compact('packages'));
    }

    public function property_detail($slug)
    {
        $property = Property::where('slug', $slug)->first();
        if (!$property) {
            return redirect()->route('front.home')->with('error', 'Property not found');
        }

        return view('front.property_detail', compact('property'));
    }

    public function property_send_message(Request $request,$id)
    {
        $property = Property::where('id',$id)->first();
        if (!$property) {
            return redirect()->route('home')->with('error', 'Property not found');
        }

        // Send Email
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

    public function locations()
    {
        // get the total propertywise locations. which location has more property, that will come first and in this way.
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

    public function location($slug) 
    {
        $location = Location::where('slug', $slug)->first();
        if (!$location) {
            return redirect()->route('front.locations')->with('error', 'Location not found');
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

    public function agents()
    {
        $agents = Agent::where('status', 1)->orderBy('id', 'asc')->paginate(20);
        return view('front.agents', compact('agents'));
    }
    
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

    public function property_search(Request $request)
    {
        $form_name = $request->name;
        $form_location = $request->location;
        $form_type = $request->type;
        $form_purpose = $request->purpose;
        $form_bedroom = $request->bedroom;
        $form_bathroom = $request->bathroom;
        $form_min_price = $request->min_price;
        $form_max_price = $request->max_price;
        $form_amenity = $request->amenity;

        $properties = Property::where('status', 'Active')
            ->whereHas('agent', function($query) {
                $query->whereHas('orders', function($q) {
                    $q->where('currently_active', 1)
                        ->where('status', 'Completed')
                        ->where('expire_date', '>=', now());
                });
            });

        if ($request->name != null) {
            $properties = $properties->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if($request->location!= null) {
            $properties = $properties->where('location_id', $request->location);
        }

        if($request->type!= null) {
            $properties = $properties->where('type_id', $request->type);
        }

        if($request->purpose!= null) {
            $properties = $properties->where('purpose', $request->purpose);
        }

        if($request->bedroom!= null) {
            $properties = $properties->where('bedroom', $request->bedroom);
        }

        if($request->bathroom!= null) {
            $properties = $properties->where('bathroom', $request->bathroom);
        }

        if($request->min_price != null) {
            $properties = $properties->where('price', '>=', $request->min_price);
        }
        
        if($request->max_price != null) {
            $properties = $properties->where('price', '<=', $request->max_price);
        }

        if($request->amenity!= null) {
            $properties = $properties->whereRaw('FIND_IN_SET(?, amenities)', [$request->amenity]);
        }
    

        $properties = $properties->orderBy('id', 'asc')->paginate(10);

        $locations = Location::orderBy('name', 'asc')->get();
        $types = Type::orderBy('name', 'asc')->get();
        $amenities = Amenity::orderBy('name', 'asc')->get();

        return view('front.property_search', compact('locations', 'types', 'amenities', 'properties', 'form_name', 'form_location', 'form_type', 'form_purpose', 'form_bedroom', 'form_bathroom', 'form_min_price', 'form_max_price', 'form_amenity'));
    }

    public function wishlist_add($id)
    {
        if(!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('error', 'Please login to add property to wishlist');
        }

        // Check if the property is already in the wishlist
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

    public function subscriber_send_email(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'email' => ['required','email','unique:subscribers,email'],
        ]);

        if(!$validator->passes()) {
            return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);
        } else {

            $token = hash('sha256', time());

            $obj = new Subscriber();
            $obj->email = $request->email;
            $obj->token = $token;
            $obj->status = 0;
            $obj->save();

            $verification_link = url('subscriber/verify/'.$request->email.'/'.$token);
            
            // Send email
            $subject = 'Subscriber Verification';
            $message = 'Please click on the link below to confirm subscription:<br>';
            $message .= '<a href="'.$verification_link.'">';
            $message .= $verification_link;
            $message .= '</a>';

            \Mail::to($request->email)->send(new Websitemail($subject,$message));

            return response()->json(['code'=>1,'success_message'=>'Please check your email to confirm subscription']);
        }
    }

    public function subscriber_verify($email,$token)
    {
        $subscriber_data = Subscriber::where('email',$email)->where('token',$token)->first();

        if($subscriber_data) {
            
            $subscriber_data->token = '';
            $subscriber_data->status = 1;
            $subscriber_data->update();

            return redirect()->route('home')->with('success', 'Your subscription is verified successfully!');

        } else {
            return redirect()->route('home');
        }

    }

    public function terms()
    {
        $terms_data = Page::where('id',1)->first();
        return view('front.terms', compact('terms_data'));
    }

    public function privacy()
    {
        $privacy_data = Page::where('id',1)->first();
        return view('front.privacy', compact('privacy_data'));
    }
    
}
