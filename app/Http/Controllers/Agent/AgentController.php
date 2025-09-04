<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail};
use App\Mail\Websitemail;
use App\Models\{Agent, Package, Order, Admin, Property, Location, Type, Amenity, PropertyPhoto, PropertyVideo, Message, MessageReply, User};
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AgentController extends Controller
{
    /*───────────────────────────────────────────────────────────────────────────
    الدالة: dashboard
    الغرض: لوحة تحكم الوكيل + إحصاءات أساسية وقائمة آخر عقاراته
    ───────────────────────────────────────────────────────────────────────────*/
    public function dashboard()
    {
        $aid = Auth::guard('agent')->user()->id; // reuse agent id

        // Basic counters (Active/Pending/Featured)
        $total_active_properties   = Property::where('agent_id', $aid)->where('status', 'Active')->count();
        $total_pending_properties  = Property::where('agent_id', $aid)->where('status', 'Pending')->count();
        $total_featured_properties = Property::where('agent_id', $aid)->where('status', 'Active')->where('is_featured', 'Yes')->count();

        // Recent 5 active properties
        $recent_properties = Property::where('agent_id', $aid)->where('status', 'Active')->orderBy('id','desc')->take(5)->get();

        return view('agent.dashboard.index', compact('total_active_properties','total_pending_properties','total_featured_properties', 'recent_properties'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration
    الغرض: عرض صفحة تسجيل الوكيل (نفس صفحة اليوزر حسب مشروعك)
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration()
    {
        // Reuse user registration view by design
        return view('user.auth.registration');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration_submit
    الغرض: إنشاء وكيل + إرسال رابط التحقق
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration_submit(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'username'        => 'required|string|max:255|unique:agents,username',
            'email'           => 'required|max:255|email|unique:agents,email',
            'company'         => 'required|string|max:255',
            'phone'           => 'required|string|max:255',
            'password'        => 'required',
            'confirm_password'=> 'required|same:password',
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
        $message = 'Click on the following link to verify your email: <br><a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return back()->with('success', 'تم تسجيل حسابك بنجاح. يرجى التحقق من بريدك الإلكتروني للتحقق من حسابك.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration_verify
    الغرض: تفعيل حساب الوكيل عبر الرابط
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration_verify($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if (!$agent) {
            return redirect()->route('agent_login')->with('error', 'خطأ في التحقق. الرابط أو البريد الإلكتروني غير صالح.');
        }
        $agent->token  = '';
        $agent->status = 1;
        $agent->update();

        return redirect()->route('agent_login')->with('success', 'Email verified successfully. You can now login.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login
    الغرض: عرض صفحة تسجيل الدخول
    ───────────────────────────────────────────────────────────────────────────*/
    public function login()
    {
        // Reuse user login view by design
        return view('user.auth.login');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login_submit
    الغرض: تسجيل دخول وكيل (بالإيميل أو اليوزر) بشرط أن يكون مفعّل
    ───────────────────────────────────────────────────────────────────────────*/
    public function login_submit(Request $request)
    {
        $request->validate([
            'email'    => 'required', // email or username
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('agent')->attempt([
            $fieldType => $request->email,
            'password' => $request->password,
            'status'   => 1,
        ])) {
            return redirect()->route('agent_dashboard')->with('success', 'تم تسجيل الدخول بنجاح.');
        }

        return back()->with('error', 'معلومات الدخول غير صحيحة أو الحساب غير مفعّل.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: logout
    الغرض: تسجيل الخروج من جلسة الوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function logout()
    {
        Auth::guard('agent')->logout();
        return redirect()->route('agent_login')->with('success', 'Logged out successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: forget_password
    الغرض: عرض صفحة نسيان كلمة المرور
    ───────────────────────────────────────────────────────────────────────────*/
    public function forget_password()
    {
        return view('agent.auth.forget_password');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: forget_password_submit
    الغرض: إرسال رابط إعادة تعيين كلمة المرور
    ───────────────────────────────────────────────────────────────────────────*/
    public function forget_password_submit(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $agent = Agent::where('email', $request->email)->first();
        if (!$agent){
            return back()->with('error', 'لم يتم العثور على البريد الإلكتروني في سجلاتنا.');
        }

        $token        = hash('sha256', time());
        $agent->token = $token;
        $agent->update();

        $link    = route('agent_reset_password', [$token,$request->email]);
        $subject = 'Reset Password';
        $message = 'انقر على الرابط لاعادة تعيين كلمة السر: <br><a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject,$message));

        return back()->with('success', 'تم ارسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: reset_password
    الغرض: عرض صفحة إعادة التعيين بعد التحقق من الرموز
    ───────────────────────────────────────────────────────────────────────────*/
    public function reset_password($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if(!$agent){
            return redirect()->route('agent_login')->with('error', 'Invalid token or email');
        }
        return view('agent.auth.reset_password', compact('token', 'email'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: reset_password_submit
    الغرض: حفظ كلمة السر الجديدة وتفريغ التوكن
    ───────────────────────────────────────────────────────────────────────────*/
    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if (!$agent){
            return redirect()->route('agent_login')->with('error', 'Invalid token or email');
        }

        $agent->password = Hash::make($request->password);
        $agent->token    = '';
        $agent->update();

        return redirect()->route('agent_login')->with('success', 'Password reset successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile
    الغرض: عرض صفحة الملف الشخصي للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile()
    {
        return view('agent.profile.index');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile_submit
    الغرض: تحديث بيانات الوكيل (التحقق كما هو)
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile_submit(Request $request)
    {
        $aid = Auth::guard('agent')->user()->id;

        $request->validate([
            'name'   => 'required',
            'email'  => 'required|email|unique:agents,email,'.$aid,
            'company'=> 'required',
        ]);

        $agent = Agent::findOrFail($aid);

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
            $final_name = 'agent_'.time().'.'.$request->photo->extension();

            if ($agent->photo && file_exists(public_path('uploads/'.$agent->photo))) {
                @unlink(public_path('uploads/'.$agent->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $agent->photo = $final_name;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password'         => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $agent->password = Hash::make($request->password);
        }

        // Fill remaining fields
        $agent->name        = $request->name;
        $agent->email       = $request->email;
        $agent->company     = $request->company;
        $agent->designation = $request->designation;
        $agent->phone       = $request->phone;
        $agent->address     = $request->address;
        $agent->country     = $request->country;
        $agent->state       = $request->state;
        $agent->city        = $request->city;
        $agent->zip         = $request->zip;
        $agent->facebook    = $request->facebook;
        $agent->twitter     = $request->twitter;
        $agent->linkedin    = $request->linkedin;
        $agent->instagram   = $request->instagram;
        $agent->website     = $request->website;
        $agent->biography   = $request->biography;
        $agent->update();

        return back()->with('success', 'Profile updated successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: order
    الغرض: عرض قائمة طلبات/اشتراكات الوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function order()
    {
        $orders = Order::where('agent_id', Auth::guard('agent')->user()->id)->orderBy('id','desc')->get();
        return view('agent.order.index', compact('orders'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: invoice
    الغرض: عرض فاتورة طلب معيّن
    ───────────────────────────────────────────────────────────────────────────*/
    public function invoice($id)
    {
        $order = Order::where('id',$id)->first();
        if(!$order){
            return back()->with('error', 'Order not found');
        }
        return view('agent.order.invoice', compact('order'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: payment
    الغرض: شاشة الدفع (باقة حالية + الأيام المتبقية)
    ───────────────────────────────────────────────────────────────────────────*/
    public function payment()
    {
        $aid = Auth::guard('agent')->user()->id;

        $total_current_order = Order::where('agent_id', $aid)->count();
        $packages            = Package::orderBy('id','asc')->get();
        $current_order       = Order::where('agent_id', $aid)->where('currently_active',1)->first();

        $days_left = 0;
        if ($current_order) {
            $days_left = (strtotime($current_order->expire_date) - strtotime(date('Y-m-d'))) / 86400;
        }

        return view('agent.payment.index', compact('packages','total_current_order', 'current_order', 'days_left'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: paypal
    الغرض: إنشاء طلب PayPal والانتقال لصفحة الموافقة
    ───────────────────────────────────────────────────────────────────────────*/
    public function paypal(Request $request)
    {
        $package = Package::where('id',$request->package_id)->first();
        if (!$package) {
            return redirect()->route('agent_payment')->with('error', 'Package not found.');
        }

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

        if(isset($response['id']) && $response['id']) {
            foreach($response['links'] as $link) {
                if($link['rel'] === 'approve') {
                    session()->put('package_id', $request->package_id);
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: paypal_success
    الغرض: إكمال عملية PayPal وتسجيل الطلب + رسائل البريد
    ───────────────────────────────────────────────────────────────────────────*/
    public function paypal_success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if(isset($response['status']) && $response['status'] === 'COMPLETED') {

            $package = Package::where('id',session()->get('package_id'))->first();
            $invoice_no = 'INV-'.Auth::guard('agent')->user()->id.'-'.time();
            $admin      = Admin::where('id',1)->first();

            // deactivate previous orders
            Order::where('agent_id', Auth::guard('agent')->user()->id)->update(['currently_active' => 0]);

            // create order
            $order = new Order;
            $order->agent_id        = Auth::guard('agent')->user()->id;
            $order->package_id      = session()->get('package_id');
            $order->invoice_no      = $invoice_no;
            $order->transaction_id  = $response['id'] ?? null; // response array
            $order->payment_method  = 'PayPal';
            $order->paid_amount     = $package->price;
            $order->purchase_date   = date('Y-m-d');
            $order->expire_date     = date('Y-m-d', strtotime('+'.$package->allowed_days.' days'));
            $order->status          = 'Completed';
            $order->currently_active= 1;
            $order->save();

            // email to agent
            $link    = route('agent_order');
            $subject = 'Payment Successful';
            $message = 'Dear '.Auth::guard('agent')->user()->name.', <br><br>';
            $message .= 'Your payment has been successfully processed. Payment information is given below:<br><br>';
            $message .= 'Invoice No: '.$invoice_no.'<br>';
            $message .= 'Payment Method: PayPal<br>';
            $message .= 'Transaction ID: '.($response['id'] ?? '').'<br>';
            $message .= 'Package Name: '.$package->name.'<br>';
            $message .= 'Paid Amount: $'.$package->price.'<br>';
            $message .= 'Purchase Date: '.date('Y-m-d').'<br>';
            $message .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package->allowed_days.' days')).'<br><br>';
            $message .= 'Click on the following link to view your order: <br>';
            $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
            $message .= 'Thank you for your order!<br><br>Best Regards,<br>'.env('APP_NAME');

            Mail::to(Auth::guard('agent')->user()->email)->send(new Websitemail($subject,$message));

            // email to admin
            $alink   = route('admin_order_index');
            $asub    = 'New Order Received';
            $amsg    = 'Dear Admin, <br><br>';
            $amsg   .= 'A new order has been received. Payment information is given below:<br><br>';
            $amsg   .= 'Invoice No: '.$invoice_no.'<br>';
            $amsg   .= 'Agent Name: '.Auth::guard('agent')->user()->name.'<br>';
            $amsg   .= 'Agent Email: '.Auth::guard('agent')->user()->email.'<br>';
            $amsg   .= 'Payment Method: PayPal<br>';
            $amsg   .= 'Transaction ID: '.($response['id'] ?? '').'<br>';
            $amsg   .= 'Package Name: '.$package->name.'<br>';
            $amsg   .= 'Paid Amount: $'.$package->price.'<br>';
            $amsg   .= 'Purchase Date: '.date('Y-m-d').'<br>';
            $amsg   .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package->allowed_days.' days')).'<br><br>';
            $amsg   .= 'Click on the following link to view the order: <br>';
            $amsg   .= '<a href="'.$alink.'">'.$alink.'</a><br><br>';
            $amsg   .= 'Thank you!<br><br>Best Regards,<br>'.env('APP_NAME');

            if ($admin && $admin->email) {
                Mail::to($admin->email)->send(new Websitemail($asub,$amsg));
            }

            session()->forget('package_id');

            return redirect()->route('agent_order')->with('success', 'Payment successful. Your order has been placed.');
        }

        return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: stripe
    الغرض: إنشاء جلسة دفع Stripe والانتقال لصفحة الدفع
    ───────────────────────────────────────────────────────────────────────────*/
    public function stripe(Request $request)
    {
        $package = Package::where('id',$request->package_id)->first();
        if (!$package) {
            return redirect()->route('agent_payment')->with('error', 'Package not found.');
        }

        $stripe   = new \Stripe\StripeClient(config('stripe.stripe_sk'));
        $response = $stripe->checkout->sessions->create([
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

        if(isset($response->id) && $response->id){
            session()->put('package_id', $request->package_id);
            return redirect($response->url);
        }

        return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: stripe_success
    الغرض: إكمال Stripe وتسجيل الطلب + رسائل البريد
    ───────────────────────────────────────────────────────────────────────────*/
    public function stripe_success(Request $request)
    {
        if (!isset($request->session_id)) {
            return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
        }

        $stripe   = new \Stripe\StripeClient(config('stripe.stripe_sk'));
        $response = $stripe->checkout->sessions->retrieve($request->session_id);

        $package    = Package::where('id',session()->get('package_id'))->first();
        $invoice_no = 'INV-'.Auth::guard('agent')->user()->id.'-'.time();
        $admin      = Admin::where('id',1)->first();

        // deactivate previous
        Order::where('agent_id', Auth::guard('agent')->user()->id)->update(['currently_active' => 0]);

        // create order
        $order = new Order;
        $order->agent_id        = Auth::guard('agent')->user()->id;
        $order->package_id      = session()->get('package_id');
        $order->invoice_no      = $invoice_no;
        $order->transaction_id  = $response->id ?? null;
        $order->payment_method  = 'Stripe';
        $order->paid_amount     = $package->price;
        $order->purchase_date   = date('Y-m-d');
        $order->expire_date     = date('Y-m-d', strtotime('+'.$package->allowed_days.' days'));
        $order->status          = 'Completed';
        $order->currently_active= 1;
        $order->save();

        // email to agent
        $link    = route('agent_order');
        $subject = 'Payment Successful';
        $message = 'Dear '.Auth::guard('agent')->user()->name.', <br><br>';
        $message .= 'Your payment has been successfully processed. Payment information is given below:<br><br>';
        $message .= 'Invoice No: '.$invoice_no.'<br>';
        $message .= 'Payment Method: Stripe<br>';
        $message .= 'Transaction ID: '.($response->id ?? '').'<br>';
        $message .= 'Package Name: '.$package->name.'<br>';
        $message .= 'Paid Amount: $'.$package->price.'<br>';
        $message .= 'Purchase Date: '.date('Y-m-d').'<br>';
        $message .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package->allowed_days.' days')).'<br><br>';
        $message .= 'Click on the following link to view your order: <br>';
        $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
        $message .= 'Thank you for your order!<br><br>Best Regards,<br>'.env('APP_NAME');

        Mail::to(Auth::guard('agent')->user()->email)->send(new Websitemail($subject,$message));

        // email to admin
        $alink   = route('admin_order_index');
        $asub    = 'New Order Received';
        $amsg    = 'Dear Admin, <br><br>';
        $amsg   .= 'A new order has been received. Payment information is given below:<br><br>';
        $amsg   .= 'Invoice No: '.$invoice_no.'<br>';
        $amsg   .= 'Agent Name: '.Auth::guard('agent')->user()->name.'<br>';
        $amsg   .= 'Agent Email: '.Auth::guard('agent')->user()->email.'<br>';
        $amsg   .= 'Payment Method: Stripe<br>';
        $amsg   .= 'Transaction ID: '.($response->id ?? '').'<br>';
        $amsg   .= 'Package Name: '.$package->name.'<br>';
        $amsg   .= 'Paid Amount: $'.$package->price.'<br>';
        $amsg   .= 'Purchase Date: '.date('Y-m-d').'<br>';
        $amsg   .= 'Expire Date: '.date('Y-m-d', strtotime('+'.$package->allowed_days.' days')).'<br><br>';
        $amsg   .= 'Click on the following link to view the order: <br>';
        $amsg   .= '<a href="'.$alink.'">'.$alink.'</a><br><br>';
        $amsg   .= 'Thank you!<br><br>Best Regards,<br>'.env('APP_NAME');

        if ($admin && $admin->email) {
            Mail::to($admin->email)->send(new Websitemail($asub,$amsg));
        }

        session()->forget('package_id');

        return redirect()->route('agent_order')->with('success', 'Payment successful. Your order has been placed.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_all
    الغرض: قائمة عقارات الوكيل (يتطلب باقة مفعّلة)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_all()
    {
        $aid = Auth::guard('agent')->user()->id;

        $order = Order::where('agent_id', $aid)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to see properties.');
        }

        $properties = Property::where('agent_id', $aid)->get();
        return view('agent.property.index', compact('properties'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_create
    الغرض: نموذج إنشاء عقار (فحوص الباقة + الحدود)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_create()
    {
        $aid   = Auth::guard('agent')->user()->id;
        $order = Order::where('agent_id', $aid)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }

        if($order->package->allowed_properties <= Property::where('agent_id', $aid)->count()){
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }

        if($order->expire_date < date('Y-m-d')){
            return redirect()->route('agent_payment')->with('error', 'Your package has been expired. Please purchase a new package to create properties.');
        }

        $locations = Location::orderBy('id','asc')->get();
        $types     = Type::orderBy('id','asc')->get();
        $amenities = Amenity::orderBy('id','asc')->get();

        return view('agent.property.create', compact('locations', 'types', 'amenities'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_store
    الغرض: حفظ عقار جديد (مع فحوص الباقة + المميّز)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_store(Request $request)
    {
        $aid   = Auth::guard('agent')->user()->id;
        $order = Order::where('agent_id', $aid)->where('currently_active',1)->first();

        if($request->is_featured == 'Yes' && $order){
            if($order->package->allowed_featured_properties <= Property::where('agent_id', $aid)->where('is_featured','Yes')->count()){
                return back()->with('error', 'You have reached the maximum number of featured properties allowed in your package. Please purchase a new package to create more featured properties.');
            }
        }

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

        // Upload featured photo
        $final_name = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();
        $request->featured_photo->move(public_path('uploads'), $final_name);

        $amenitiesCsv = is_array($request->amenity) ? implode(',', $request->amenity) : null; // safe implode

        $property = new Property();
        $property->agent_id       = $aid;
        $property->location_id    = $request->location_id;
        $property->type_id        = $request->type_id;
        $property->amenities      = $amenitiesCsv;
        $property->name           = $request->name;
        $property->slug           = $request->slug;
        $property->description    = $request->description;
        $property->price          = $request->price;
        $property->featured_photo = $final_name;
        $property->purpose        = $request->purpose;
        $property->bedroom        = $request->bedroom;
        $property->bathroom       = $request->bathroom;
        $property->size           = $request->size;
        $property->floor          = $request->floor;
        $property->garage         = $request->garage;
        $property->balcony        = $request->balcony;
        $property->address        = $request->address;
        $property->built_year     = $request->built_year;
        $property->map            = $request->map;
        $property->is_featured    = $request->is_featured;
        $property->status         = 'Pending';
        $property->save();

        // Notify admin
        $admin   = Admin::where('id',1)->first();
        $admin_email = $admin?->email;
        if ($admin_email) {
            $link    = route('admin_property_index');
            $subject = 'A new property has been added';
            $message = 'Please check the following link to see the pending property that is currently added to the system:<br>';
            $message .= '<a href="'.$link.'">'.$link.'</a><br><br>';
            Mail::to($admin_email)->send(new Websitemail($subject, $message));
        }

        return redirect()->route('agent_property_index')->with('success', 'Property created successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_edit
    الغرض: نموذج تعديل عقار مملوك للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_edit($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }
        $existing_amenities = $property->amenities ? explode(',', $property->amenities) : [];

        $locations = Location::orderBy('id','asc')->get();
        $types     = Type::orderBy('id','asc')->get();
        $amenities = Amenity::orderBy('id','asc')->get();

        return view('agent.property.edit', compact('property', 'locations', 'types', 'amenities', 'existing_amenities'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_update
    الغرض: حفظ تعديلات عقار مملوك للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_update(Request $request, $id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }

        $request->validate([
            'name'     => ['required'],
            'slug'     => ['required','unique:properties,slug,'.$property->id, 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'    => ['required'],
            'size'     => ['required', 'numeric'],
            'bedroom'  => ['required', 'numeric'],
            'bathroom' => ['required', 'numeric'],
            'address'  => ['required'],
        ]);

        if ($request->hasFile('featured_photo')) {
            $request->validate(['featured_photo' => ['image','mimes:jpeg,png,jpg,gif,svg','max:2048']]);
            $final_name = 'property_f_photo_'.time().'.'.$request->featured_photo->extension();

            if ($property->featured_photo && file_exists(public_path('uploads/'.$property->featured_photo))) {
                @unlink(public_path('uploads/'.$property->featured_photo));
            }
            $request->featured_photo->move(public_path('uploads'), $final_name);
            $property->featured_photo = $final_name;
        }

        $property->location_id = $request->location_id;
        $property->type_id     = $request->type_id;
        $property->amenities   = is_array($request->amenity) ? implode(',', $request->amenity) : null;
        $property->name        = $request->name;
        $property->slug        = $request->slug;
        $property->description = $request->description;
        $property->price       = $request->price;
        $property->purpose     = $request->purpose;
        $property->bedroom     = $request->bedroom;
        $property->bathroom    = $request->bathroom;
        $property->size        = $request->size;
        $property->floor       = $request->floor;
        $property->garage      = $request->garage;
        $property->balcony     = $request->balcony;
        $property->address     = $request->address;
        $property->built_year  = $request->built_year;
        $property->map         = $request->map;
        $property->is_featured = $request->is_featured;
        $property->update();

        return redirect()->route('agent_property_index')->with('success', 'Property updated successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_delete
    الغرض: حذف عقار مملوك للوكيل + حذف ملف الصورة وملفات الألبوم
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_delete($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }

        if ($property->featured_photo && file_exists(public_path('uploads/'.$property->featured_photo))) {
            @unlink(public_path('uploads/'.$property->featured_photo));
        }

        $photos = PropertyPhoto::where('property_id',$property->id)->get();
        foreach($photos as $photo){
            if ($photo->photo && file_exists(public_path('uploads/'.$photo->photo))) {
                @unlink(public_path('uploads/'.$photo->photo));
            }
        }

        $property->delete();

        return back()->with('success', 'Property deleted successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_photo_gallery
    الغرض: عرض صور ألبوم عقار
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_photo_gallery($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }

        $photos = PropertyPhoto::where('property_id',$property->id)->get();

        return view('agent.property.photo_gallery', compact('property', 'photos'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_photo_gallery_store
    الغرض: إضافة صورة جديدة لألبوم عقار (مع فحوص الباقة/الحدود)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_photo_gallery_store(Request $request, $id)
    {
        $aid   = Auth::guard('agent')->user()->id;
        $order = Order::where('agent_id', $aid)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }

        if($order->package->allowed_properties <= Property::where('agent_id', $aid)->count()){
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }

        if($order->package->allowed_photos <= PropertyPhoto::where('property_id',$id)->count()){
            return back()->with('error', 'You have reached the maximum number of photos allowed in your package. Please purchase a new package to add more photos.');
        }

        $property = Property::where('id',$id)->where('agent_id', $aid)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }

        $request->validate([
            'photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
        ]);

        $final_name = 'property_photo_'.time().'.'.$request->photo->extension();
        $request->photo->move(public_path('uploads'), $final_name);

        $obj = new PropertyPhoto();
        $obj->property_id = $property->id;
        $obj->photo       = $final_name;
        $obj->save();

        return back()->with('success', 'Photo added successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_photo_gallery_delete
    الغرض: حذف صورة من الألبوم
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_photo_gallery_delete($id)
    {
        $photo = PropertyPhoto::where('id',$id)->first();
        if(!$photo){
            return back()->with('error', 'Photo not found');
        }
        if ($photo->photo && file_exists(public_path('uploads/'.$photo->photo))) {
            @unlink(public_path('uploads/'.$photo->photo));
        }
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_video_gallery
    الغرض: عرض فيديوهات عقار
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_video_gallery($id)
    {
        $property = Property::where('id',$id)->where('agent_id', Auth::guard('agent')->user()->id)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }

        $videos = PropertyVideo::where('property_id',$property->id)->get();

        return view('agent.property.video_gallery', compact('property', 'videos'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_video_gallery_store
    الغرض: إضافة فيديو جديد لعقار (مع فحوص الباقة/الحدود)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_video_gallery_store(Request $request, $id)
    {
        $aid   = Auth::guard('agent')->user()->id;
        $order = Order::where('agent_id', $aid)->where('currently_active',1)->first();
        if(!$order){
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }

        if($order->package->allowed_properties <= Property::where('agent_id', $aid)->count()){
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }

        if($order->package->allowed_videos <= PropertyVideo::where('property_id',$id)->count()){
            return back()->with('error', 'You have reached the maximum number of videos allowed in your package. Please purchase a new package to add more videos.');
        }

        $property = Property::where('id',$id)->where('agent_id', $aid)->first();
        if(!$property){
            return back()->with('error', 'Property not found');
        }

        $request->validate(['video' => ['required']]);

        $obj = new PropertyVideo();
        $obj->property_id = $property->id;
        $obj->video       = $request->video;
        $obj->save();

        return back()->with('success', 'Video added successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_video_gallery_delete
    الغرض: حذف فيديو من عقار
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_video_gallery_delete($id)
    {
        $video = PropertyVideo::where('id',$id)->first();
        if(!$video){
            return back()->with('error', 'Video not found');
        }
        $video->delete();

        return back()->with('success', 'Video deleted successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message
    الغرض: رسائل الوكيل الواردة من العملاء
    ───────────────────────────────────────────────────────────────────────────*/
    public function message()
    {
        $messages = Message::where('agent_id', Auth::guard('agent')->user()->id)->get();
        return view('agent.message.index', compact('messages'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply
    الغرض: صفحة الردود على رسالة معينة
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply($id)
    {
        $message = Message::where('id', $id)->first();
        $replies = MessageReply::where('message_id', $id)->get();
        return view('agent.message.reply', compact('message', 'replies'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply_submit
    الغرض: إرسال رد من الوكيل إلى العميل + إشعار العميل بالبريد
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply_submit(Request $request, $m_id, $c_id)
    {
        $request->validate(['reply' => 'required']);

        $reply = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id    = $c_id;
        $reply->agent_id   = Auth::guard('agent')->user()->id;
        $reply->sender     = 'Agent';
        $reply->reply      = $request->reply;
        $reply->save();

        $subject = 'New Reply from Agent';
        $body    = 'You have received a new reply from agent. Please click on the following link:<br>';
        $link    = url('message/reply/'.$m_id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        $user = User::where('id', $c_id)->first();
        if ($user) {
            Mail::to($user->email)->send(new Websitemail($subject, $body));
        }

        return back()->with('success', 'Reply sent successfully');
    }
}
