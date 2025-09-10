<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail, DB};
use App\Mail\Websitemail;
use App\Models\{
    Agent, Package, Order, Property, Location, Type, Amenity,
    PropertyPhoto, PropertyVideo, Message, MessageReply, User, PropertyRentalRule
};
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\StripeClient;

class AgentController extends Controller
{
    /*───────────────────────────────────────────────────────────────────────────
    الدالة: dashboard
    الغرض: لوحة تحكم الوكيل + إحصاءات أساسية وقائمة آخر عقاراته
    ───────────────────────────────────────────────────────────────────────────*/
    public function dashboard()
    {
        $aid = Auth::guard('agent')->id();

        $total_active_properties  = Property::where('agent_id',$aid)->active()->count();
        $total_pending_properties = Property::where('agent_id',$aid)->pending()->count();
        $total_featured_properties= Property::where('agent_id',$aid)->active()->featured()->count();

        $recent_properties = Property::where('agent_id',$aid)->active()->orderByDesc('id')->take(5)->get();


        return view('agent.dashboard.index', compact(
            'total_active_properties',
            'total_pending_properties',
            'total_featured_properties',
            'recent_properties'
        ));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration
    الغرض: عرض صفحة تسجيل الوكيل (نفس صفحات المستخدم حسب مشروعك)
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration()
    {
        if (Auth::guard('agent')->check()) return redirect()->route('agent_dashboard');
        if (Auth::guard('web')->check())   return redirect()->route('dashboard');
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
        $message = 'Click on the following link to verify your email: <br><a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return back()->with('success', 'تم تسجيل حسابك بنجاح. يرجى التحقق من بريدك الإلكتروني لتفعيل الحساب.');
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
        $agent->save();

        return redirect()->route('agent_login')->with('success', 'Email verified successfully. You can now login.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login
    الغرض: عرض صفحة تسجيل الدخول للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function login()
    {
        if (Auth::guard('agent')->check()) return redirect()->route('agent_dashboard');
        if (Auth::guard('web')->check())   return redirect()->route('dashboard');
        return view('user.auth.login');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login_submit
    الغرض: تسجيل دخول وكيل (بإيميل أو يوزرنيم) بشرط أن يكون مفعّل
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
        if (!$agent) {
            return back()->with('error', 'لم يتم العثور على البريد الإلكتروني في سجلاتنا.');
        }

        $token       = hash('sha256', time());
        $agent->token = $token;
        $agent->save();

        $link    = route('agent_reset_password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'انقر على الرابط لإعادة تعيين كلمة المرور: <br><a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: reset_password
    الغرض: عرض صفحة إعادة التعيين بعد التحقق من الرموز
    ───────────────────────────────────────────────────────────────────────────*/
    public function reset_password($token, $email)
    {
        $agent = Agent::where('email', $email)->where('token', $token)->first();
        if (!$agent) {
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
        if (!$agent) {
            return redirect()->route('agent_login')->with('error', 'Invalid token or email');
        }

        $agent->password = Hash::make($request->password);
        $agent->token    = '';
        $agent->save();

        return redirect()->route('agent_login')->with('success', 'Password reset successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile
    الغرض: عرض صفحة الملف الشخصي للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile()
    {
        $agent = Auth::guard('agent')->user();
        return view('agent.profile.index', compact('agent'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile_submit
    الغرض: تحديث بيانات الوكيل (مع تحقق ورفع صورة اختياري)
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile_submit(Request $request)
    {
        $aid = Auth::guard('agent')->id();

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:agents,email,' . $aid,
            'company'    => 'required|string|max:255',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'password'   => 'nullable',
            'confirm_password' => 'nullable|same:password',
            'designation'=> 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:255',
            'address'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:255',
            'website'    => 'nullable|string|max:255',
            'facebook'   => 'nullable|string|max:255',
            'twitter'    => 'nullable|string|max:255',
            'telegram'   => 'nullable|string|max:255',
            'instagram'  => 'nullable|string|max:255',
            'biography'  => 'nullable|string',
        ]);

        $agent = Agent::findOrFail($aid);

        // upload dir
        $uploadDir = public_path('uploads');
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

        // Photo upload (optional)
        if ($request->hasFile('photo')) {
            $final_name = 'agent_' . time() . '.' . $request->photo->extension();
            if ($agent->photo && file_exists($uploadDir . '/' . $agent->photo)) {
                @unlink($uploadDir . '/' . $agent->photo);
            }
            $request->photo->move($uploadDir, $final_name);
            $agent->photo = $final_name;
        }

        // Password (optional)
        if ($request->filled('password')) {
            $request->validate([
                'password'         => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $agent->password = Hash::make($request->password);
        }

        // Fill safe fields
        $agent->name        = $request->name;
        $agent->email       = $request->email;
        $agent->company     = $request->company;
        $agent->designation = $request->designation;
        $agent->phone       = $request->phone;
        $agent->address     = $request->address;
        $agent->city        = $request->city;
        $agent->website     = $request->website;
        $agent->facebook    = $request->facebook;
        $agent->twitter     = $request->twitter;
        $agent->telegram    = $request->telegram;
        $agent->instagram   = $request->instagram;
        $agent->biography   = $request->biography;
        $agent->save();

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: order
    الغرض: عرض قائمة طلبات/اشتراكات الوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function order()
    {
        $orders = Order::where('agent_id', Auth::guard('agent')->id())
            ->orderBy('id', 'desc')->get();
        return view('agent.order.index', compact('orders'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: invoice
    الغرض: عرض فاتورة طلب معيّن
    ───────────────────────────────────────────────────────────────────────────*/
    public function invoice($id)
    {
        $order = Order::find($id);
        if (!$order) return back()->with('error', 'Order not found');
        return view('agent.order.invoice', compact('order'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: payment
    الغرض: شاشة الدفع (عرض الباقة الحالية + الأيام المتبقية)
    ───────────────────────────────────────────────────────────────────────────*/
    public function payment()
    {
        $aid = Auth::guard('agent')->id();

        // Activate any due scheduled order
        $this->activateFutureOrderIfDue($aid);

        $total_current_order = Order::where('agent_id', $aid)->count();
        $packages     = Package::orderBy('price', 'asc')->get();
        $currentOrder = Order::where('agent_id', $aid)->where('currently_active', 1)->first();

        $days_left = 0;
        if ($currentOrder) {
            $days_left = (strtotime($currentOrder->expire_date) - strtotime(date('Y-m-d'))) / 86400;
        }

        return view('agent.payment.index', compact('packages', 'total_current_order', 'currentOrder', 'days_left'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: paypal
    الغرض: إنشاء طلب PayPal والانتقال لصفحة الموافقة
    ───────────────────────────────────────────────────────────────────────────*/
    public function paypal(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = Package::findOrFail($request->package_id);

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
                "amount" => ["currency_code" => "USD", "value" => (string)$package->price]
            ]]
        ]);

        if (isset($response['id']) && $response['id']) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    session()->put('package_id', $package->id);
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: paypal_cancel
    الغرض: إلغاء عملية PayPal
    ───────────────────────────────────────────────────────────────────────────*/
    public function paypal_cancel()
    {
        return redirect()->route('agent_payment')->with('error', 'Payment cancelled.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: paypal_success
    الغرض: إكمال PayPal وتسجيل الطلب داخل معاملة + تفعيل/جدولة حسب الحالة
    ───────────────────────────────────────────────────────────────────────────*/
    public function paypal_success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (!session()->has('package_id')) {
            return redirect()->route('agent_payment')->with('error', 'Session expired. Please try again.');
        }

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            $agentId = Auth::guard('agent')->id();
            $package = Package::findOrFail(session('package_id'));
            $invoice = 'INV-' . $agentId . '-' . time();

            // (Optional sanity check) Paid amount matches package price
            $paid = (float)($response['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0);
            if (abs($paid - (float)$package->price) > 0.01) {
                return redirect()->route('agent_payment')->with('error', 'Payment amount mismatch.');
            }

            [$purchaseDate, $expireDate, $activeNow, $kind] = $this->buildOrderDatesFor($agentId, $package);

            DB::transaction(function () use ($agentId, $package, $invoice, $response, $purchaseDate, $expireDate, $activeNow, $kind) {
                if ($kind === 'upgrade') {
                    Order::where('agent_id', $agentId)->where('currently_active', 1)->update(['currently_active' => 0]);
                }

                $order = new Order();
                $order->agent_id         = $agentId;
                $order->package_id       = $package->id;
                $order->invoice_no       = $invoice;
                $order->transaction_id   = $response['id'] ?? null;
                $order->payment_method   = 'PayPal';
                $order->paid_amount      = $package->price;
                $order->purchase_date    = $purchaseDate;
                $order->expire_date      = $expireDate;
                $order->status           = 'Completed';
                $order->currently_active = $activeNow ? 1 : 0;
                $order->save();
            });

            session()->forget('package_id');

            return redirect()->route('agent_order')->with('success',
                $kind === 'upgrade' ? 'تمت الترقية وتفعيلها فورًا.'
                    : ($kind === 'renew' ? 'تم جدولة التجديد ليبدأ عند نهاية اشتراكك الحالي.'
                    : 'تم جدولة التخفيض ليبدأ عند نهاية اشتراكك الحالي.')
            );
        }

        return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: stripe
    الغرض: إنشاء جلسة دفع Stripe والانتقال لصفحة الدفع
    ───────────────────────────────────────────────────────────────────────────*/
    public function stripe(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = Package::findOrFail($request->package_id);

        $stripe = new StripeClient(config('stripe.stripe_sk'));
        $response = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $package->name],
                    'unit_amount'  => (int)round($package->price * 100),
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('agent_stripe_success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('agent_stripe_cancel'),
        ]);

        if (isset($response->id) && $response->id) {
            session()->put('package_id', $package->id);
            return redirect($response->url);
        }

        return redirect()->route('agent_payment')->with('error', 'Payment failed. Please try again.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: stripe_success
    الغرض: إكمال Stripe وتسجيل الطلب داخل معاملة + تفعيل/جدولة حسب الحالة
    ───────────────────────────────────────────────────────────────────────────*/
    public function stripe_success(Request $request)
    {
        if (!isset($request->session_id) || !session()->has('package_id')) {
            return redirect()->route('agent_payment')->with('error', 'Session expired. Please try again.');
        }

        $stripe   = new StripeClient(config('stripe.stripe_sk'));
        $response = $stripe->checkout->sessions->retrieve($request->session_id);

        $agentId = Auth::guard('agent')->id();
        $package = Package::findOrFail(session('package_id'));
        $invoice = 'INV-' . $agentId . '-' . time();

        [$purchaseDate, $expireDate, $activeNow, $kind] = $this->buildOrderDatesFor($agentId, $package);

        DB::transaction(function () use ($agentId, $package, $invoice, $response, $purchaseDate, $expireDate, $activeNow, $kind) {
            if ($kind === 'upgrade') {
                Order::where('agent_id', $agentId)->where('currently_active', 1)->update(['currently_active' => 0]);
            }

            $order = new Order();
            $order->agent_id         = $agentId;
            $order->package_id       = $package->id;
            $order->invoice_no       = $invoice;
            $order->transaction_id   = $response->id ?? null;
            $order->payment_method   = 'Stripe';
            $order->paid_amount      = $package->price;
            $order->purchase_date    = $purchaseDate;
            $order->expire_date      = $expireDate;
            $order->status           = 'Completed';
            $order->currently_active = $activeNow ? 1 : 0;
            $order->save();
        });

        session()->forget('package_id');

        return redirect()->route('agent_order')->with('success',
            $kind === 'upgrade' ? 'تمت الترقية وتفعيلها فورًا.'
                : ($kind === 'renew' ? 'تم جدولة التجديد ليبدأ عند نهاية اشتراكك الحالي.'
                : 'تم جدولة التخفيض ليبدأ عند نهاية اشتراكك الحالي.')
        );
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: stripe_cancel
    الغرض: إلغاء عملية Stripe
    ───────────────────────────────────────────────────────────────────────────*/
    public function stripe_cancel()
    {
        return redirect()->route('agent_payment')->with('error', 'Payment cancelled.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_all
    الغرض: قائمة عقارات الوكيل (يتطلب باقة مفعّلة)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_all()
    {
        $aid = Auth::guard('agent')->id();

        $this->activateFutureOrderIfDue($aid);

        $order = Order::where('agent_id', $aid)->where('currently_active', 1)->first();
        if (!$order) {
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
        $aid = Auth::guard('agent')->id();

        $this->activateFutureOrderIfDue($aid);

        $order = Order::where('agent_id', $aid)->where('currently_active', 1)->first();
        if (!$order) {
            return redirect()->route('agent_payment')->with('error', 'You have not purchased any package yet. Please purchase a package to create properties.');
        }

        if ($order->package->allowed_properties <= Property::where('agent_id', $aid)->count()) {
            return redirect()->route('agent_payment')->with('error', 'You have reached the maximum number of properties allowed in your package. Please purchase a new package to create more properties.');
        }

        if ($order->expire_date < date('Y-m-d')) {
            return redirect()->route('agent_payment')->with('error', 'Your package has been expired. Please purchase a new package to create properties.');
        }

        $locations = Location::orderBy('id')->get();
        $types     = Type::orderBy('id')->get();
        $amenities = Amenity::orderBy('id')->get();

        return view('agent.property.create', compact('locations', 'types', 'amenities'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_store
    الغرض: حفظ عقار جديد (مع فحوص الباقة + المميّز)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_store(Request $request)
    {
        $aid = Auth::guard('agent')->id();

        $request->validate([
            'name'            => ['required'],
            'slug'            => ['required', 'unique:properties,slug', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'           => ['required'],
            'size'            => ['required', 'numeric'],
            'bedroom'         => ['required', 'numeric'],
            'bathroom'        => ['required', 'numeric'],
            'address'         => ['required'],
            'featured_photo'  => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:4096'],
            'purpose'         => ['required', 'in:buy,rent'],
            'location_id'     => ['required', 'exists:locations,id'],
            'type_id'         => ['required', 'exists:types,id'],
            'gallery_photos.*'=> ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:4096'],
            'video_url'       => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $uploadDir = public_path('uploads');
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

            // Featured photo
            $final_name = 'property_f_photo_' . time() . '.' . $request->featured_photo->extension();
            $request->featured_photo->move($uploadDir, $final_name);

            $property = new Property();
            $property->agent_id       = $aid;
            $property->location_id    = $request->location_id;
            $property->type_id        = $request->type_id;
            $property->name           = $request->name;
            $property->slug           = $request->slug;
            $property->description    = $request->description;
            $property->price          = $request->price;
            $property->featured_photo = $final_name;
            $property->purpose        = $request->purpose; // buy / rent
            $property->bedroom        = $request->bedroom;
            $property->bathroom       = $request->bathroom;
            $property->size           = $request->size;
            $property->floor          = $request->floor;
            $property->garage         = $request->garage;
            $property->balcony        = $request->balcony;
            $property->address        = $request->address;
            $property->built_year     = $request->built_year;
            $property->is_featured    = $request->is_featured;
            $property->status         = 'Pending';
            $property->area           = $request->area;
            $property->map            = $this->sanitizeMap($request->map);

            // حقول تخص البيع فقط
            if ($request->purpose === 'buy') {
                $property->registry_number       = $request->registry_number;
                $property->registry_zone         = $request->registry_zone;
                $property->building_permit_no    = $request->building_permit_no;
                $property->ownership_type        = $request->ownership_type;
                $property->zoning_class          = $request->zoning_class;
                $property->build_code_compliance = $request->boolean('build_code_compliance');
                $property->earthquake_resistance = $request->boolean('earthquake_resistance');
                $property->legal_notes           = $request->legal_notes;
            }

            $property->save();

            // Amenities
            $amenities = array_map('intval', (array)$request->input('amenity', []));
            $property->amenities()->sync($amenities);

            // Gallery photos (optional)
            if ($request->hasFile('gallery_photos')) {
                $i = 0;
                foreach ($request->file('gallery_photos') as $file) {
                    if (!$file->isValid()) continue;
                    $gn = 'property_photo_' . time() . '_' . $i . '.' . $file->extension();
                    $file->move($uploadDir, $gn);
                    PropertyPhoto::create(['property_id' => $property->id, 'photo' => $gn]);
                    $i++;
                }
            }

            // Video (YouTube URL/ID)
            if ($request->filled('video_url')) {
                $ytId = $this->extractYouTubeId($request->video_url);
                if (!$ytId) {
                    throw new \RuntimeException('Please enter a valid YouTube URL or ID.');
                }
                PropertyVideo::create(['property_id' => $property->id, 'video' => $ytId]);
            }

            // Rental rules
            if ($request->purpose === 'rent' && is_array($request->rental_rule ?? null)) {
                foreach ($request->rental_rule as $key => $value) {
                    if ($value === null || $value === '') continue;
                    PropertyRentalRule::create([
                        'property_id' => $property->id,
                        'rule_key'    => $key,
                        'rule_value'  => $value,
                        'is_enforced' => 1,
                        'notes'       => null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('agent_property_index')->with('success', 'Property created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($final_name) && file_exists(public_path('uploads/' . $final_name))) {
                @unlink(public_path('uploads/' . $final_name));
            }
            return back()->with('error', 'Failed to create property: ' . $e->getMessage())->withInput();
        }
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_show
    الغرض: عرض عقار مملوك للوكيل + صوره وفيديوهاته
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_show($id)
    {
        $property = Property::where('id', $id)
            ->where('agent_id', Auth::guard('agent')->id())
            ->first();

        if (!$property) return back()->with('error', 'Property not found');

        $photos = PropertyPhoto::where('property_id', $property->id)->get();
        $videos = PropertyVideo::where('property_id', $property->id)->get();

        return view('agent.property.show', compact('property', 'photos', 'videos'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_edit
    الغرض: نموذج تعديل عقار مملوك للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_edit($id)
    {
        $agentId  = Auth::guard('agent')->id();
        $property = Property::with(['amenities:id'])
            ->where('id', $id)
            ->where('agent_id', $agentId)
            ->first();

        if (!$property) {
            return redirect()->route('agent_property_index')->with('error', 'Property not found');
        }

        session()->forget('error');

        // Normalize purpose to UI labels (backward compat)
        $p = strtolower((string)$property->purpose);
        if ($p === 'buy')  $property->purpose = 'Sale';
        if ($p === 'rent') $property->purpose = 'Rent';

        $existing_amenities = $property->amenities->pluck('id')->toArray();

        $locations    = Location::orderBy('id')->get();
        $types        = Type::orderBy('id')->get();
        $amenities    = Amenity::orderBy('id')->get();
        $rental_rules = PropertyRentalRule::where('property_id', $property->id)->get()->keyBy('rule_key');

        return view('agent.property.edit', compact(
            'property','locations','types','amenities','existing_amenities','rental_rules'
        ));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_update
    الغرض: حفظ تعديلات عقار مملوك للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_update(Request $request, $id)
    {
        $property = Property::where('id', $id)
            ->where('agent_id', Auth::guard('agent')->id())
            ->first();

        if (!$property) return back()->with('error', 'Property not found');

        $request->validate([
            'name'           => ['required'],
            'slug'           => ['required', 'unique:properties,slug,' . $property->id, 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'price'          => ['required'],
            'size'           => ['required', 'numeric'],
            'bedroom'        => ['required', 'numeric'],
            'bathroom'       => ['required', 'numeric'],
            'address'        => ['required'],
            'featured_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:4096'],
            'video_url'      => ['nullable', 'string', 'max:255'],
        ]);

        // Unify purpose input
        $incomingPurpose = strtolower((string)$request->input('purpose', $property->purpose));
        if ($incomingPurpose === 'sale') $incomingPurpose = 'buy';
        if (!in_array($incomingPurpose, ['buy', 'rent'], true)) {
            $incomingPurpose = $property->purpose;
        }

        DB::beginTransaction();
        try {
            $uploadDir = public_path('uploads');
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

            // Featured photo (optional)
            if ($request->hasFile('featured_photo')) {
                $finalName = 'property_f_photo_' . time() . '.' . $request->featured_photo->extension();
                if ($property->featured_photo && file_exists($uploadDir . '/' . $property->featured_photo)) {
                    @unlink($uploadDir . '/' . $property->featured_photo);
                }
                $request->featured_photo->move($uploadDir, $finalName);
                $property->featured_photo = $finalName;
            }

            // Base fields
            $property->location_id = $request->location_id ?? $property->location_id;
            $property->type_id     = $request->type_id ?? $property->type_id;
            $property->name        = $request->name;
            $property->slug        = $request->slug;
            $property->description = $request->description;
            $property->price       = $request->price;
            $property->purpose     = $incomingPurpose; // buy | rent
            $property->bedroom     = $request->bedroom;
            $property->bathroom    = $request->bathroom;
            $property->size        = $request->size;
            $property->floor       = $request->floor;
            $property->garage      = $request->garage;
            $property->balcony     = $request->balcony;
            $property->address     = $request->address;
            $property->built_year  = $request->built_year;
            $property->map         = $request->has('map') ? $this->sanitizeMap($request->map) : $property->map;
            $property->is_featured = $request->is_featured ?? $property->is_featured;
            $property->area        = $request->area ?? $property->area;

            // Sale-only fields (update if present)
            foreach ([
                         'registry_number', 'registry_zone', 'building_permit_no', 'ownership_type',
                         'zoning_class', 'build_code_compliance', 'earthquake_resistance', 'legal_notes'
                     ] as $col) {
                if ($request->has($col)) {
                    if (in_array($col, ['build_code_compliance','earthquake_resistance'], true)) {
                        $property->{$col} = $request->boolean($col);
                    } else {
                        $property->{$col} = $request->input($col);
                    }
                }
            }

            $property->save();

            // Amenities
            $amenities = array_map('intval', (array)$request->input('amenity', []));
            $property->amenities()->sync($amenities);

            // Extra video (optional) — store YouTube ID only
            if ($request->filled('video_url')) {
                $ytId = $this->extractYouTubeId($request->video_url);
                if (!$ytId) {
                    throw new \RuntimeException('Please enter a valid YouTube URL or ID.');
                }
                PropertyVideo::create([
                    'property_id' => $property->id,
                    'video'       => $ytId,
                ]);
            }

            // Extra gallery photos (optional)
            if ($request->hasFile('gallery_photos')) {
                foreach ($request->file('gallery_photos') as $file) {
                    if (!$file->isValid()) continue;
                    $name = 'property_photo_' . time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move($uploadDir, $name);
                    PropertyPhoto::create(['property_id' => $property->id, 'photo' => $name]);
                }
            }

            // Rental rules (for rent)
            if ($incomingPurpose === 'rent' && is_array($request->rental_rule)) {
                $keys = ['payment_cycle','deposit_amount','stamp_fee','damages_policy','handover_time','short_term_allowed'];
                foreach ($keys as $key) {
                    if (!array_key_exists($key, $request->rental_rule)) continue;
                    $val  = $request->rental_rule[$key];
                    $rule = PropertyRentalRule::firstOrNew([
                        'property_id' => $property->id,
                        'rule_key'    => $key,
                    ]);
                    $rule->rule_value = $val;
                    $rule->save();
                }
            }

            DB::commit();
            return redirect()->route('agent_property_index')->with('success', 'Property updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update property: ' . $e->getMessage())->withInput();
        }
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_delete
    الغرض: حذف عقار مملوك للوكيل + حذف ملف الصورة وسجلات الألبوم
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_delete($id)
    {
        $property = Property::where('id', $id)
            ->where('agent_id', Auth::guard('agent')->id())
            ->first();

        if (!$property) return back()->with('error', 'Property not found');

        $uploadDir = public_path('uploads');

        if ($property->featured_photo && file_exists($uploadDir . '/' . $property->featured_photo)) {
            @unlink($uploadDir . '/' . $property->featured_photo);
        }

        $photos = PropertyPhoto::where('property_id', $property->id)->get();
        foreach ($photos as $photo) {
            if ($photo->photo && file_exists($uploadDir . '/' . $photo->photo)) {
                @unlink($uploadDir . '/' . $photo->photo);
            }
        }

        // delete DB rows for photos/videos
        PropertyPhoto::where('property_id', $property->id)->delete();
        PropertyVideo::where('property_id', $property->id)->delete();

        $property->delete();

        return back()->with('success', 'Property deleted successfully');
    }

    /* ===================== ألبوم الصور ===================== */

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_photo_gallery
    الغرض: عرض ألبوم الصور لعقار الوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_photo_gallery($id)
    {
        $aid = Auth::guard('agent')->id();

        $property = Property::where('id', $id)->where('agent_id', $aid)->first();
        if (!$property) return back()->with('error', 'Property not found');

        $photos = PropertyPhoto::where('property_id', $property->id)->orderByDesc('id')->get();

        return view('agent.property.photo_gallery', compact('property', 'photos'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_photo_gallery_store
    الغرض: إضافة صورة واحدة للمعرض
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_photo_gallery_store(Request $request, $id)
    {
        $aid = Auth::guard('agent')->id();

        $property = Property::where('id', $id)->where('agent_id', $aid)->first();
        if (!$property) return back()->with('error', 'Property not found');

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:4096'],
        ]);

        if (!$request->file('photo')->isValid()) {
            return back()->with('error', 'Invalid photo upload.');
        }

        $uploadDir = public_path('uploads');
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

        $finalName = 'property_photo_' . time() . '_' . uniqid() . '.' . $request->photo->extension();
        $request->photo->move($uploadDir, $finalName);

        PropertyPhoto::create(['property_id' => $property->id, 'photo' => $finalName]);

        return back()->with('success', 'Photo added successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_photo_gallery_delete
    الغرض: حذف صورة من الألبوم
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_photo_gallery_delete($photoId)
    {
        $aid = Auth::guard('agent')->id();

        $photo = PropertyPhoto::find($photoId);
        if (!$photo) return back()->with('error', 'Photo not found');

        $property = Property::where('id', $photo->property_id)->where('agent_id', $aid)->first();
        if (!$property) return back()->with('error', 'Not authorized');

        $path = public_path('uploads/' . $photo->photo);
        if (is_file($path)) @unlink($path);

        $photo->delete();

        return back()->with('success', 'Photo deleted');
    }

    /* ===================== ألبوم الفيديو ===================== */

    /*───────────────────────────────────────────────────────────────────────────
    أداة: extractYouTubeId
    الغرض: استخراج ID يوتيوب من الرابط أو من ID خام
    ───────────────────────────────────────────────────────────────────────────*/
    private function extractYouTubeId(string $input): ?string
    {
        $s = trim($input);

        // raw ID
        if (preg_match('~^[A-Za-z0-9_-]{8,64}$~', $s)) return $s;

        // watch?v= / youtu.be / shorts / embed
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i', $s, $m)) {
            return $m[1];
        }

        return null;
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_video_gallery
    الغرض: عرض ألبوم الفيديو للعقار
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_video_gallery($id)
    {
        $aid = Auth::guard('agent')->id();

        $property = Property::where('id', $id)->where('agent_id', $aid)->first();
        if (!$property) return back()->with('error', 'Property not found');

        $videos = PropertyVideo::where('property_id', $property->id)->orderByDesc('id')->get();

        return view('agent.property.video_gallery', compact('property', 'videos'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_video_gallery_store
    الغرض: إضافة فيديو (الحقل يقبل رابط أو ID يوتيوب)
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_video_gallery_store(Request $request, $id)
    {
        $aid = Auth::guard('agent')->id();

        $property = Property::where('id', $id)->where('agent_id', $aid)->first();
        if (!$property) return back()->with('error', 'Property not found');

        $request->validate(['video' => ['required', 'string', 'max:255']]);

        $ytId = $this->extractYouTubeId($request->video);
        if (!$ytId) {
            return back()->with('error', 'Please enter a valid YouTube URL or ID.')->withInput();
        }

        PropertyVideo::create(['property_id' => $property->id, 'video' => $ytId]);

        return back()->with('success', 'Video added successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: property_video_gallery_delete
    الغرض: حذف فيديو من العقار
    ───────────────────────────────────────────────────────────────────────────*/
    public function property_video_gallery_delete($videoId)
    {
        $aid = Auth::guard('agent')->id();

        $video = PropertyVideo::find($videoId);
        if (!$video) return back()->with('error', 'Video not found');

        $property = Property::where('id', $video->property_id)->where('agent_id', $aid)->first();
        if (!$property) return back()->with('error', 'Not authorized');

        $video->delete();

        return back()->with('success', 'Video deleted');
    }

    /* ===================== الرسائل ===================== */

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message
    الغرض: رسائل الوكيل الواردة من العملاء (قائمة)
    ───────────────────────────────────────────────────────────────────────────*/
    public function message()
    {
        $agentId = Auth::guard('agent')->id();

        $messages = Message::with(['user:id,name,email,phone,photo'])
            ->where('agent_id', $agentId)
            ->latest()
            ->paginate(10);

        return view('agent.message.index', compact('messages'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply
    الغرض: صفحة الردود على رسالة محددة
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply($id)
    {
        $agentId = Auth::guard('agent')->id();

        $message = Message::with(['user:id,name,email,phone,photo'])
            ->where('id', $id)->where('agent_id', $agentId)->first();

        if (!$message) {
            return redirect()->route('agent_message')->with('error', 'Message not found');
        }

        $replies = MessageReply::with(['user:id,name,photo', 'agent:id,name,photo'])
            ->where('message_id', $message->id)
            ->orderBy('id')
            ->get();

        return view('agent.message.reply', compact('message', 'replies'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply_submit
    الغرض: إرسال رد من الوكيل للعميل + إشعار بريد
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply_submit(Request $request, $m_id, $c_id)
    {
        $request->validate(['reply' => 'required|string']);

        $agentId = Auth::guard('agent')->id();

        $message = Message::where('id', $m_id)
            ->where('agent_id', $agentId)
            ->where('user_id', $c_id)
            ->first();

        if (!$message) {
            return back()->with('error', 'Message not found or not authorized.');
        }

        $reply = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id    = $c_id;
        $reply->agent_id   = $agentId;
        $reply->sender     = 'Agent';
        $reply->reply      = $request->reply;
        $reply->save();

        // Notify customer
        if ($user = User::find($c_id)) {
            $subject = 'New Reply from Agent';
            $body    = 'You have received a new reply from agent. Please click on the following link:<br>';
            $link    = route('message_reply', $m_id); // ✅ link to customer thread
            $body   .= '<a href="' . $link . '">' . $link . '</a>';
            Mail::to($user->email)->send(new Websitemail($subject, $body));
        }

        return back()->with('success', 'Reply sent successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_delete
    الغرض: حذف رسالة كاملة + كل الردود (للوكيل فقط)
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_delete($id)
    {
        $agentId = Auth::guard('agent')->id();

        $message = Message::where('id', $id)->where('agent_id', $agentId)->first();
        if (!$message) {
            return back()->with('error', 'Message not found or not authorized.');
        }

        MessageReply::where('message_id', $message->id)->delete();
        $message->delete();

        return redirect()->route('agent_message')->with('success', 'Message deleted');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply_delete
    الغرض: حذف رد واحد (ردود الوكيل فقط)
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply_delete($replyId)
    {
        $agentId = Auth::guard('agent')->id();

        $reply = MessageReply::where('id', $replyId)
            ->where('agent_id', $agentId) // لا يحذف رد العميل
            ->first();

        if (!$reply) {
            return back()->with('error', 'Reply not found or not authorized.');
        }

        $reply->delete();
        return back()->with('success', 'Reply deleted');
    }

    /* ===================== Helpers خاصة بالاشتراكات ===================== */

    // تفعيل أي طلب مجدول حلّ موعده (يوم الشراء <= اليوم)
    private function activateFutureOrderIfDue(int $agentId): void
    {
        $current = Order::where('agent_id', $agentId)->where('currently_active', 1)->first();
        if ($current && $current->expire_date < date('Y-m-d')) {
            $current->currently_active = 0;
            $current->save();
            $current = null;
        }

        $future = Order::where('agent_id', $agentId)
            ->where('currently_active', 0)
            ->whereNotNull('purchase_date')
            ->whereDate('purchase_date', '<=', date('Y-m-d'))
            ->orderBy('id')
            ->first();

        if (!$current && $future) {
            $future->currently_active = 1;
            if ($future->purchase_date > date('Y-m-d')) {
                $future->purchase_date = date('Y-m-d');
            }
            $future->save();
        }
    }

    // حساب تواريخ/حالة الطلب الجديد حسب الباقة الحالية (upgrade/renew/downgrade)
    private function buildOrderDatesFor(int $agentId, Package $newPkg): array
    {
        $today   = date('Y-m-d');
        $current = Order::where('agent_id', $agentId)->where('currently_active', 1)->first();

        if (!$current || $current->expire_date < $today) {
            return [$today, date('Y-m-d', strtotime("+{$newPkg->allowed_days} days", strtotime($today))), 1, 'fresh'];
        }

        $oldPkg   = $current->package;
        $oldPrice = (float)($oldPkg->price ?? 0);
        $newPrice = (float)$newPkg->price;

        if ($newPrice > $oldPrice) {
            $daysLeft  = max(0, (int)floor((strtotime($current->expire_date) - strtotime($today)) / 86400));
            $oldDaily  = $oldPrice / max(1, (int)$oldPkg->allowed_days);
            $newDaily  = $newPrice / max(1, (int)$newPkg->allowed_days);
            $creditDays= (int)floor(($daysLeft * $oldDaily) / max(0.00001, $newDaily));

            $purchase = $today;
            $expire   = date('Y-m-d', strtotime("+" . ($newPkg->allowed_days + $creditDays) . " days", strtotime($today)));
            return [$purchase, $expire, 1, 'upgrade'];
        }

        if ($newPrice == $oldPrice) {
            $startFrom = date('Y-m-d', strtotime($current->expire_date . ' +1 day'));
            $expire    = date('Y-m-d', strtotime("+{$newPkg->allowed_days} days", strtotime($startFrom)));
            return [$startFrom, $expire, 0, 'renew'];
        }

        $startFrom = date('Y-m-d', strtotime($current->expire_date . ' +1 day'));
        $expire    = date('Y-m-d', strtotime("+{$newPkg->allowed_days} days", strtotime($startFrom)));
        return [$startFrom, $expire, 0, 'downgrade'];
    }

    /* ===================== Helpers للتطهير ===================== */

    // Sanitization بسيط وآمن لتضمين iframe خرائط (نحتفظ بالـ src فقط من نطاقات موثوقة)
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
}
