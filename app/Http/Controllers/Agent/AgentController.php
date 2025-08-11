<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\Models\Agent;
use App\Models\Package;
use App\Models\Order;
use App\Models\Admin;
use App\Models\Property;
use App\Models\Location;
use App\Models\Type;
use App\Models\Amenity;
use App\Models\PropertyPhoto;
use App\Models\PropertyVideo;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
use App\Mail\Websitemail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AgentController extends Controller
{
    public function dashboard()
    {
        $total_active_properties = Property::where('agent_id', Auth::guard('agent')->user()->id)->where('status', 'Active')->count();
        $total_pending_properties = Property::where('agent_id', Auth::guard('agent')->user()->id)->where('status', 'Pending')->count();
        $total_featured_properties = Property::where('agent_id', Auth::guard('agent')->user()->id)->where('status', 'Active')->where('is_featured', 'Yes')->count();

        $recent_properties = Property::where('agent_id', Auth::guard('agent')->user()->id)->where('status', 'Active')->orderBy('id','desc')->take(5)->get();

        return view('agent.dashboard.index', compact('total_active_properties','total_pending_properties','total_featured_properties', 'recent_properties'));
    }

    public function registration()
    {
        return view('agent.auth.registration');
    }

    public function registration_submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|max:255|email|unique:users,email',
            'company' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $token = hash('sha256', time());

        $agent = new Agent();
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->company = $request->company;
        $agent->designation = $request->designation;
        $agent->password = Hash::make($request->password);
        $agent->token = $token;
        $agent->save();

        $link = url('agent/registration-verify/'.$token.'/'.$request->email);
        $subject = 'Registration Verification';
        $message = 'Click on the following link to verify your email: <br><a href="' . $link . '">' . $link . '</a>';

        \Mail::to($request->email)->send(new Websitemail($subject, $message));
        return redirect()->back()->with('success', 'Registration successful. Please check your email to verify your account.');
    }

    public function registration_verify($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if (!$agent) {
            return redirect()->route('agent_login')->with('error', 'Invalid token or email');
        }
        $agent->token = '';
        $agent->status = 1;
        $agent->update();

        return redirect()->route('agent_login')->with('success', 'Email verified successfully. You can now login.');
    }

    public function login()
    {
        return view('agent.auth.login');
    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
            'status' => 1,
        ];

        if(Auth::guard('agent')->attempt($data)){
            return redirect()->route('agent_dashboard')->with('success', 'Logged in successfully');
        } else {
            return redirect()->back()->with('error', 'Invalid credentials');
        }
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        return redirect()->route('agent_login')->with('success', 'Logged out successfully');
    }

    public function forget_password()
    {
        return view('agent.auth.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $agent = Agent::where('email', $request->email)->first();
        if(!$agent){
            return redirect()->back()->with('error', 'Email not found');
        }

        $token = hash('sha256', time());
        $agent->token = $token;
        $agent->update();

        $link = route('agent_reset_password', [$token,$request->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to reset your password: <br>';
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        \Mail::to($request->email)->send(new Websitemail($subject,$message));

        return redirect()->back()->with('success', 'Reset password link sent to your email');

    }

    public function reset_password($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if(!$agent){
            return redirect()->route('agent_login')->with('error', 'Invalid token or email');
        }
        return view('agent.auth.reset_password', compact('token', 'email'));
    }

    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $agent = Agent::where('email', $email)->where('token', $token)->first();
        $agent->password = Hash::make($request->password);
        $agent->token = '';
        $agent->update();

        return redirect()->route('agent_login')->with('success', 'Password reset successfully');
    }

    public function profile()
    {
        return view('agent.profile.index');
    }

    public function profile_submit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:agents,email,'.Auth::guard('agent')->user()->id,
            'company' => 'required',
            'designation' => 'required', 
        ]);

        $agent = Agent::where('id',Auth::guard('agent')->user()->id)->first();

        if($request->photo){
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $final_name = 'agent_'.time().'.'.$request->photo->extension();
            if($agent->photo != '') {
                unlink(public_path('uploads/'.$agent->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $agent->photo = $final_name;
        }

        if($request->password){
            $request->validate([
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $agent->password = Hash::make($request->password);
        }
        
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->company = $request->company;
        $agent->designation = $request->designation;
        $agent->phone = $request->phone;
        $agent->address = $request->address;
        $agent->country = $request->country;
        $agent->state = $request->state;
        $agent->city = $request->city;
        $agent->zip = $request->zip;
        $agent->facebook = $request->facebook;
        $agent->twitter = $request->twitter;
        $agent->linkedin = $request->linkedin;
        $agent->instagram = $request->instagram;
        $agent->website = $request->website;
        $agent->biography = $request->biography;
        $agent->update();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function order()
    {
        $orders = Order::orderBy('id','desc')->where('agent_id', Auth::guard('agent')->user()->id)->get();
        return view('agent.order.index', compact('orders'));
    }

    public function invoice($id)
    {
        $order = Order::where('id',$id)->first();
        if(!$order){
            return redirect()->back()->with('error', 'Order not found');
        }
        return view('agent.order.invoice', compact('order'));
    }

    public function payment()
    {
        $total_current_order = Order::where('agent_id', Auth::guard('agent')->user()->id)->count();
        $packages = Package::orderBy('id','asc')->get();
        $current_order = Order::where('agent_id', Auth::guard('agent')->user()->id)->where('currently_active',1)->first();

        // How many days left for the current order
        if($current_order){
            $days_left = (strtotime($current_order->expire_date) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
        } else {
            $days_left = 0;
        }

        return view('agent.payment.index', compact('packages','total_current_order', 'current_order', 'days_left'));
    }

    public function paypal(Request $request)
    {
        //dd($request->all());

        $package_data = Package::where('id',$request->package_id)->first();

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('agent_paypal_success'),
                "cancel_url" => route('agent_paypal_cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $package_data->price
                    ]
                ]
            ]
        ]);
        //dd($response);
        if(isset($response['id']) && $response['id'] != null) {
            foreach($response['links'] as $link) {
                if($link['rel'] == 'approve') {
                    session()->put('package_id', $request->package_id);
                    return redirect()->away($link['href']);
                }
            }
        } else {
            return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
        }
    }

    public function paypal_success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);
        //dd($response);
        if(isset($response['status']) && $response['status'] == 'COMPLETED') {

            $package_data = Package::where('id',session()->get('package_id'))->first();
            $invoice_no = 'INV-'.Auth::guard('agent')->user()->id.'-'.time();
            $admin_data = Admin::where('id',1)->first();
            
            // All previous orders will be currently_active as 0
            Order::where('agent_id', Auth::guard('agent')->user()->id)->update(['currently_active' => 0]);

            // Insert data into database
            $order = new Order;
            $order->agent_id = Auth::guard('agent')->user()->id;
            $order->package_id = session()->get('package_id');
            $order->invoice_no = $invoice_no;
            $order->transaction_id = $response['id'];
            $order->payment_method = 'PayPal';
            $order->paid_amount = $package_data->price;
            $order->purchase_date = date('Y-m-d');
            $order->expire_date = date('Y-m-d', strtotime('+'.$package_data->allowed_days.' days'));
            $order->status = 'Completed';
            $order->currently_active = 1;
            $order->save();

            // Sending email to user
            $link = route('agent_order');
            $subject = 'Payment Successful';
            $message = 'Dear '.Auth::guard('agent')->user()->name.', <br><br>';
            $message .= 'Your payment has been successfully processed. Payment information is given below:<br><br>';
            $message .= 'Invoice No: '.$invoice_no.'<br>';
            $message .= 'Payment Method: PayPal<br>';
            $message .= 'Transaction ID: '.$response->id.'<br>';
            $message .= 'Package Name: '.$package_data->name.'<br>';
            $message .= 'Paid Amount: $'.$package_data->price.'<br>';
            $message .= 'Purchase Date: '.date('Y-m-d').'<br>';
            $message .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package_data->allowed_days.' days')).'<br><br>';
            $message .= 'Click on the following link to view your order: <br>';
            $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
            $message .= 'Thank you for your order!<br><br>';
            $message .= 'Best Regards,<br>';
            $message .= env('APP_NAME');

            \Mail::to(Auth::guard('agent')->user()->email)->send(new Websitemail($subject,$message));

            // Sending email to admin
            $link = route('admin_order_index');
            $subject = 'New Order Received';
            $message = 'Dear Admin, <br><br>';
            $message .= 'A new order has been received. Payment information is given below:<br><br>';
            $message .= 'Invoice No: '.$invoice_no.'<br>';
            $message .= 'Agent Name: '.Auth::guard('agent')->user()->name.'<br>';
            $message .= 'Agent Email: '.Auth::guard('agent')->user()->email.'<br>';
            $message .= 'Payment Method: Stripe<br>';
            $message .= 'Transaction ID: '.$response->id.'<br>';
            $message .= 'Package Name: '.$package_data->name.'<br>';
            $message .= 'Paid Amount: $'.$package_data->price.'<br>';
            $message .= 'Purchase Date: '.date('Y-m-d').'<br>';
            $message .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package_data->allowed_days.' days')).'<br><br>';
            $message .= 'Click on the following link to view the order: <br>';
            $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
            $message .= 'Thank you!<br><br>';
            $message .= 'Best Regards,<br>';
            $message .= env('APP_NAME');

            \Mail::to($admin_data->email)->send(new Websitemail($subject,$message));

            session()->forget('package_id');

            return redirect()->route('agent_order')->with('success', 'Payment successful. Your order has been placed.');
        } else {
            return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
        }
    }

    public function stripe(Request $request)
    {
        $package_data = Package::where('id',$request->package_id)->first();

        $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));
        $response = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $package_data->name,
                        ],
                        'unit_amount' => $package_data->price*100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('agent_stripe_success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('agent_stripe_cancel'),
        ]);
        //dd($response);
        if(isset($response->id) && $response->id != ''){
            session()->put('package_id', $request->package_id);
            return redirect($response->url);
        } else {
            return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
        }
    }

    public function stripe_success(Request $request)
    {
        if(isset($request->session_id)) {

            $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
            //dd($response);

            $package_data = Package::where('id',session()->get('package_id'))->first();
            $invoice_no = 'INV-'.Auth::guard('agent')->user()->id.'-'.time();
            $admin_data = Admin::where('id',1)->first();
            
            // All previous orders will be currently_active as 0
            Order::where('agent_id', Auth::guard('agent')->user()->id)->update(['currently_active' => 0]);

            // Insert data into database
            $order = new Order;
            $order->agent_id = Auth::guard('agent')->user()->id;
            $order->package_id = session()->get('package_id');
            $order->invoice_no = $invoice_no;
            $order->transaction_id = $response->id;
            $order->payment_method = 'Stripe';
            $order->paid_amount = $package_data->price;
            $order->purchase_date = date('Y-m-d');
            $order->expire_date = date('Y-m-d', strtotime('+'.$package_data->allowed_days.' days'));
            $order->status = 'Completed';
            $order->currently_active = 1;
            $order->save();
            
            // Sending email to user
            $link = route('agent_order');
            $subject = 'Payment Successful';
            $message = 'Dear '.Auth::guard('agent')->user()->name.', <br><br>';
            $message .= 'Your payment has been successfully processed. Payment information is given below:<br><br>';
            $message .= 'Invoice No: '.$invoice_no.'<br>';
            $message .= 'Payment Method: Stripe<br>';
            $message .= 'Transaction ID: '.$response->id.'<br>';
            $message .= 'Package Name: '.$package_data->name.'<br>';
            $message .= 'Paid Amount: $'.$package_data->price.'<br>';
            $message .= 'Purchase Date: '.date('Y-m-d').'<br>';
            $message .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package_data->allowed_days.' days')).'<br><br>';
            $message .= 'Click on the following link to view your order: <br>';
            $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
            $message .= 'Thank you for your order!<br><br>';
            $message .= 'Best Regards,<br>';
            $message .= env('APP_NAME');

            \Mail::to(Auth::guard('agent')->user()->email)->send(new Websitemail($subject,$message));

            // Sending email to admin
            $link = route('admin_order_index');
            $subject = 'New Order Received';
            $message = 'Dear Admin, <br><br>';
            $message .= 'A new order has been received. Payment information is given below:<br><br>';
            $message .= 'Invoice No: '.$invoice_no.'<br>';
            $message .= 'Agent Name: '.Auth::guard('agent')->user()->name.'<br>';
            $message .= 'Agent Email: '.Auth::guard('agent')->user()->email.'<br>';
            $message .= 'Payment Method: Stripe<br>';
            $message .= 'Transaction ID: '.$response->id.'<br>';
            $message .= 'Package Name: '.$package_data->name.'<br>';
            $message .= 'Paid Amount: $'.$package_data->price.'<br>';
            $message .= 'Purchase Date: '.date('Y-m-d').'<br>';
            $message .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package_data->allowed_days.' days')).'<br><br>';
            $message .= 'Click on the following link to view the order: <br>';
            $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
            $message .= 'Thank you!<br><br>';
            $message .= 'Best Regards,<br>';
            $message .= env('APP_NAME');

            \Mail::to($admin_data->email)->send(new Websitemail($subject,$message));



            session()->forget('package_id');

            return redirect()->route('agent_order')->with('success', 'Payment successful. Your order has been placed.');
        } else {
            return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
        }
    }

    public function property_all()
    {
        // Check in orders table if this agent has any package purchased
        $order = Order::where('agent_id', Auth::guard('agent')->user()->id)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to see properties.');
        }

        $properties = Property::where('agent_id', Auth::guard('agent')->user()->id)->get();
        return view('agent.property.index', compact('properties'));
    }

    public function property_create()
    {
        // Check in orders table if this agent has any package purchased
        $order = Order::where('agent_id', Auth::guard('agent')->user()->id)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }

        // Check if the agent has reached the maximum number of properties allowed in the package
        if($order->package->allowed_properties <= Property::where('agent_id', Auth::guard('agent')->user()->id)->count()){
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }

        // Check if package has been expired
        if($order->expire_date < date('Y-m-d')){
            return redirect()->route('agent_payment')->with('error', 'Your package has been expired. Please purchase a new package to create properties.');
        }


        $locations = Location::orderBy('id','asc')->get();
        $types = Type::orderBy('id','asc')->get();
        $amenities = Amenity::orderBy('id','asc')->get();
        return view('agent.property.create', compact('locations', 'types', 'amenities'));
    }

    public function property_store(Request $request)
    {
        // Check if the featured property has been reached to maximum limit
        $order = Order::where('agent_id', Auth::guard('agent')->user()->id)->where('currently_active',1)->first();
        if($request->is_featured == 'Yes') {
            if($order->package->allowed_featured_properties <= Property::where('agent_id', Auth::guard('agent')->user()->id)->where('is_featured','Yes')->count()){
                return redirect()->back()->with('error', 'You have reached the maximum number of featured properties allowed in your package. Please purchase a new package to create more featured properties.');
            }
        }

        $request->validate([
            'name' => ['required'],
            'slug' => ['required','unique:properties,slug', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price' => ['required'],
            'size' => ['required', 'numeric'],
            'bedroom' => ['required', 'numeric'],
            'bathroom' => ['required', 'numeric'],
            'address' => ['required'],
            'featured_photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
        ]);

        $final_name = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
        $request->featured_photo->move(public_path('uploads'), $final_name);

        $property = new Property();
        $property->agent_id = Auth::guard('agent')->user()->id;
        $property->location_id = $request->location_id;
        $property->type_id = $request->type_id;
        $property->amenities = implode(',', $request->amenity);
        $property->name = $request->name;
        $property->slug = $request->slug;
        $property->description = $request->description;
        $property->price = $request->price;
        $property->featured_photo = $final_name;
        $property->purpose = $request->purpose;
        $property->bedroom = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->size = $request->size;
        $property->floor = $request->floor;
        $property->garage = $request->garage;
        $property->balcony = $request->balcony;
        $property->address = $request->address;
        $property->built_year = $request->built_year;
        $property->map = $request->map;
        $property->is_featured = $request->is_featured;
        $property->status = 'Pending';
        $property->save();

        // Send email to admin
        $admin_data = Admin::where('id',1)->first();
        $admin_email = $admin_data->email;
        $link = route('admin_property_index');
        $subject = 'A new property has been added';
        $message = 'Please check the following link to see the pending property that is currently added to the system:<br>';
        $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';

        \Mail::to($admin_email)->send(new Websitemail($subject, $message));

        return redirect()->route('agent_property_index')->with('success', 'Property created successfully');
    }

    public function property_edit($id) 
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }
        $existing_amenities = explode(',', $property->amenities);
        $locations = Location::orderBy('id','asc')->get();
        $types = Type::orderBy('id','asc')->get();
        $amenities = Amenity::orderBy('id','asc')->get();
        return view('agent.property.edit', compact('property', 'locations', 'types', 'amenities', 'existing_amenities'));
    }

    public function property_update(Request $request, $id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }

        $request->validate([
            'name' => ['required'],
            'slug' => ['required','unique:properties,slug,'.$property->id, 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price' => ['required'],
            'size' => ['required', 'numeric'],
            'bedroom' => ['required', 'numeric'],
            'bathroom' => ['required', 'numeric'],
            'address' => ['required'],
        ]);

        if($request->featured_photo){
            $request->validate([
                'featured_photo' => ['image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
            ]);
            $final_name = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
            if($property->featured_photo != '') {
                unlink(public_path('uploads/'.$property->featured_photo));
            }
            $request->featured_photo->move(public_path('uploads'), $final_name);
            $property->featured_photo = $final_name;
        }

        $property->location_id = $request->location_id;
        $property->type_id = $request->type_id;
        $property->amenities = implode(',', $request->amenity);
        $property->name = $request->name;
        $property->slug = $request->slug;
        $property->description = $request->description;
        $property->price = $request->price;
        $property->purpose = $request->purpose;
        $property->bedroom = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->size = $request->size;
        $property->floor = $request->floor;
        $property->garage = $request->garage;
        $property->balcony = $request->balcony;
        $property->address = $request->address;
        $property->built_year = $request->built_year;
        $property->map = $request->map;
        $property->is_featured = $request->is_featured;
        $property->update();

        return redirect()->route('agent_property_index')->with('success', 'Property updated successfully');
    }

    public function property_delete($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }
        if($property->featured_photo != '') {
            unlink(public_path('uploads/'.$property->featured_photo));
        }
        $photos = PropertyPhoto::where('property_id',$property->id)->get();
        foreach($photos as $photo){
            if($photo->photo != '') {
                unlink(public_path('uploads/'.$photo->photo));
            }
        }
        $property->delete();

        return redirect()->back()->with('success', 'Property deleted successfully');
    }

    public function property_photo_gallery($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }

        $photos = PropertyPhoto::where('property_id',$property->id)->get();

        return view('agent.property.photo_gallery', compact('property', 'photos'));
    }

    public function property_photo_gallery_store(Request $request, $id)
    {
        // Check in orders table if this agent has any package purchased
        $order = Order::where('agent_id', Auth::guard('agent')->user()->id)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }


        // Check if the agent has reached the maximum number of properties allowed in the package
        if($order->package->allowed_properties <= Property::where('agent_id', Auth::guard('agent')->user()->id)->count()){
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }


        // Check if the agent has reached the maximum number of photos allowed in the package
        if($order->package->allowed_photos <= PropertyPhoto::where('property_id',$id)->count()){
            return redirect()->back()->with('error', 'You have reached the maximum number of photos allowed in your package. Please purchase a new package to add more photos.');
        }


        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }

        $request->validate([
            'photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
        ]);

        $obj = new PropertyPhoto();

        $final_name = 'property_photo_'.time().'.'.$request->photo->extension();
        $request->photo->move(public_path('uploads'), $final_name);

        $obj->property_id = $property->id;
        $obj->photo = $final_name;
        $obj->save();

        return redirect()->back()->with('success', 'Photo added successfully');
    }

    public function property_photo_gallery_delete($id)
    {
        $photo = PropertyPhoto::where('id',$id)->first();
        if(!$photo){
            return redirect()->back()->with('error', 'Photo not found');
        }
        if($photo->photo != '') {
            unlink(public_path('uploads/'.$photo->photo));
        }
        $photo->delete();
        return redirect()->back()->with('success', 'Photo deleted successfully');
    }


    public function property_video_gallery($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }

        $videos = PropertyVideo::where('property_id',$property->id)->get();

        return view('agent.property.video_gallery', compact('property', 'videos'));
    }

    public function property_video_gallery_store(Request $request, $id)
    {
        // Check in orders table if this agent has any package purchased
        $order = Order::where('agent_id', Auth::guard('agent')->user()->id)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }


        // Check if the agent has reached the maximum number of properties allowed in the package
        if($order->package->allowed_properties <= Property::where('agent_id', Auth::guard('agent')->user()->id)->count()){
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }


        // Check if the agent has reached the maximum number of videos allowed in the package
        if($order->package->allowed_videos <= PropertyVideo::where('property_id',$id)->count()){
            return redirect()->back()->with('error', 'You have reached the maximum number of videos allowed in your package. Please purchase a new package to add more videos.');
        }


        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }

        $request->validate([
            'video' => ['required'],
        ]);

        $obj = new PropertyVideo();
        $obj->property_id = $property->id;
        $obj->video = $request->video;
        $obj->save();

        return redirect()->back()->with('success', 'Video added successfully');
    }

    public function property_video_gallery_delete($id)
    {
        $video = PropertyVideo::where('id',$id)->first();
        if(!$video){
            return redirect()->back()->with('error', 'Video not found');
        }
        $video->delete();
        return redirect()->back()->with('success', 'Video deleted successfully');
    }

    public function message()
    {
        $messages = Message::where('agent_id', Auth::guard('agent')->user()->id)->get();
        return view('agent.message.index', compact('messages'));
    }

    public function message_reply($id)
    {
        $message = Message::where('id', $id)->first();
        $replies = MessageReply::where('message_id', $id)->get();
        return view('agent.message.reply', compact('message', 'replies'));
    }

    public function message_reply_submit(Request $request, $m_id, $c_id)
    {
        $request->validate([
            'reply' => 'required',
        ]);

        $reply = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id = $c_id;
        $reply->agent_id = Auth::guard('agent')->user()->id;
        $reply->sender = 'Agent';
        $reply->reply = $request->reply;
        $reply->save();

        // Send email to agent
        $subject = 'New Reply from Agent';
        $message = 'You have received a new reply from agent. Please click on the following link:<br>';
        $link = url('message/reply/'.$m_id);
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        $user = User::where('id', $c_id)->first();
        
        \Mail::to($user->email)->send(new Websitemail($subject, $message));

        return redirect()->back()->with('success', 'Reply sent successfully');
    }

}
