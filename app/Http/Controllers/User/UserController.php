<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail};
use App\Mail\Websitemail;
use App\Models\{User, Agent, Wishlist, Message, MessageReply};

class UserController extends Controller
{
    /*───────────────────────────────────────────────────────────────────────────
    الدالة: dashboard
    الغرض: إظهار لوحة المستخدم مع إحصاءات الرسائل والمفضلة
    ───────────────────────────────────────────────────────────────────────────*/
    public function dashboard()
    {
        $uid  = Auth::id();
        $user = Auth::user();

        $counts = [
            'messages' => Message::where('user_id', $uid)->count(),
            'wishlist' => Wishlist::where('user_id', $uid)->count(),
        ];

        return view('user.dashboard.index', compact('counts','user'))
            ->with('activeTab','profile');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration
    الغرض: عرض صفحة تسجيل مستخدم جديد
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration()
    {
        if (Auth::guard('web')->check())   return redirect()->route('dashboard');
        if (Auth::guard('agent')->check()) return redirect()->route('agent_dashboard');
        return view('user.auth.registration');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration_submit
    الغرض: التحقق من التسجيل وإنشاء مستخدم وإرسال رابط التحقق
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration_submit(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'username'         => 'required|string|max:255|unique:users,username',
            'email'            => 'required|max:255|email|unique:users,email',
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $token = hash('sha256', time());

        $user = new User();
        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->token    = $token;
        $user->save();

        $link    = url('registration-verify/'.$token.'/'.$request->email);
        $subject = 'Registration Verification';
        $message = 'انقر على الرابط لتفعيل حسابك: <br><a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return back()->with('success', 'تم التسجيل بنجاح. تحقق من بريدك الإلكتروني لتفعيل حسابك.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration_verify
    الغرض: تفعيل الحساب بواسطة رابط التحقق
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration_verify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'بريد إلكتروني أو رمز غير صالح');
        }

        $user->token  = '';
        $user->status = 1;
        $user->save();

        return redirect()->route('login')->with('success', 'تم التحقق من حسابك. يمكنك الآن تسجيل الدخول.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login
    الغرض: عرض صفحة تسجيل الدخول
    ───────────────────────────────────────────────────────────────────────────*/
    public function login()
    {
        if (Auth::guard('web')->check())   return redirect()->route('dashboard');
        if (Auth::guard('agent')->check()) return redirect()->route('agent_dashboard');
        return view('user.auth.login');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login_submit
    الغرض: محاولة تسجيل الدخول (بالبريد أو اليوزرنيم) للمستخدم النشط
    ───────────────────────────────────────────────────────────────────────────*/
    public function login_submit(Request $request)
    {
        $request->validate([
            'email'    => 'required', // email or username
            'password' => 'required',
        ]);

        $login_input = $request->email;
        $fieldType   = filter_var($login_input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $login_input,
            'password' => $request->password,
            'status'   => 1,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
        }

        return back()->with('error', 'بيانات الدخول غير صحيحة');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: logout
    الغرض: تسجيل الخروج من جلسة الويب
    ───────────────────────────────────────────────────────────────────────────*/
    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: forget_password
    الغرض: عرض صفحة نسيان كلمة المرور
    ───────────────────────────────────────────────────────────────────────────*/
    public function forget_password()
    {
        return view('user.auth.forget_password');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: forget_password_submit
    الغرض: إرسال رابط إعادة تعيين كلمة المرور إلى البريد
    ───────────────────────────────────────────────────────────────────────────*/
    public function forget_password_submit(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user){
            return back()->with('error', 'لم يتم التعرف على البريد الإلكتروني');
        }

        $token       = hash('sha256', time());
        $user->token = $token;
        $user->save();

        $link    = route('reset_password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'انقر على الرابط لإعادة تعيين كلمة المرور: <br><a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject,$message));

        return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: reset_password
    الغرض: عرض صفحة تعيين كلمة مرور جديدة بعد التحقق من الرموز
    ───────────────────────────────────────────────────────────────────────────*/
    public function reset_password($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user){
            return redirect()->route('login')->with('error', 'Invalid token or email');
        }
        return view('user.auth.reset_password', compact('token', 'email'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: reset_password_submit
    الغرض: حفظ كلمة المرور الجديدة وتفريغ التوكن
    ───────────────────────────────────────────────────────────────────────────*/
    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid token or email');
        }

        $user->password = Hash::make($request->password);
        $user->token    = '';
        $user->save();

        return redirect()->route('login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile
    الغرض: عرض صفحة الملف الشخصي (أو التعديل عند ?edit=1)
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile(Request $request)
    {
        $uid = Auth::id();
        $counts = [
            'messages' => Message::where('user_id',$uid)->count(),
            'wishlist' => Wishlist::where('user_id',$uid)->count(),
        ];

        if ($request->boolean('edit')) {
            return view('user.profile.edit', compact('counts'))->with('activeTab','profile_edit');
        }

        return view('user.profile.index', compact('counts'))->with('activeTab','profile');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile_submit
    الغرض: تحديث بيانات المستخدم (اسم/بريد/صورة/كلمة مرور)
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile_submit(Request $request)
    {
        $uid  = Auth::guard('web')->user()->id;
        $user = User::findOrFail($uid);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,'.$uid,
            'username' => 'nullable|string|max:255|unique:users,username,'.$uid,
            'phone'    => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:255',
            'address'  => 'nullable|string|max:255',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Photo (optional)
        if ($request->hasFile('photo')) {
            $final_name = 'user_'.time().'.'.$request->photo->extension();

            $uploadDir = public_path('uploads');
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

            if ($user->photo && file_exists($uploadDir.'/'.$user->photo)) {
                @unlink($uploadDir.'/'.$user->photo);
            }

            $request->photo->move($uploadDir, $final_name);
            $user->photo = $final_name;
        }

        // Password (optional)
        if ($request->filled('password')) {
            $request->validate([
                'password'         => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->username = $request->username ?: $user->username;
        $user->phone    = $request->phone;
        $user->city     = $request->city;
        $user->address  = $request->address;
        $user->save();

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: wishlist
    الغرض: عرض قائمة المفضلة للمستخدم الحالي
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlist()
    {
        $uid = Auth::id();

        $wishlists = Wishlist::with([
            'property:id,name,slug,price,bedroom,bathroom,size,location_id,type_id,status',
            'property.location:id,name',
            'property.type:id,name',
        ])->where('user_id', $uid)->get();

        $counts = [
            'messages' => Message::where('user_id',$uid)->count(),
            'wishlist' => $wishlists->count(),
        ];

        return view('user.wishlist.index', compact('wishlists','counts'))->with('activeTab','wishlist');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: wishlist_delete
    الغرض: حذف عنصر من المفضلة (مقيد بالمستخدم الحالي)
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlist_delete($id)
    {
        $uid = Auth::id();
        Wishlist::where('id', $id)->where('user_id', $uid)->delete();
        return back()->with('success', 'Wishlist item deleted successfully');
    }

    /* ===================== رسائل المستخدم ===================== */

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message
    الغرض: عرض قائمة رسائل المستخدم
    ───────────────────────────────────────────────────────────────────────────*/
    public function message()
    {
        $uid = Auth::id();

        $messages = Message::with('agent:id,name,email,photo')
            ->where('user_id', $uid)
            ->latest('id')
            ->get();

        $counts = [
            'messages' => $messages->count(),
            'wishlist' => Wishlist::where('user_id', $uid)->count(),
        ];

        return view('user.message.index', compact('messages','counts'))->with('activeTab','messages');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_create
    الغرض: عرض نموذج إنشاء رسالة جديدة لوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_create()
    {
        $uid = Auth::id();

        $agents = Agent::where('status', 1)->orderBy('name')->get();

        $counts = [
            'messages' => Message::where('user_id', $uid)->count(),
            'wishlist' => Wishlist::where('user_id', $uid)->count(),
        ];

        return view('user.message.create', compact('agents','counts'))->with('activeTab','messages');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_store
    الغرض: تخزين رسالة جديدة + إرسال إشعار للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_store(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'subject'  => 'required|string|max:255',
            'message'  => 'required|string',
        ]);

        $msg = new Message();
        $msg->user_id  = Auth::id();
        $msg->agent_id = $request->agent_id;
        $msg->subject  = $request->subject;
        $msg->message  = $request->message;
        $msg->save();

        if ($agent = Agent::find($request->agent_id)) {
            $subject = 'New Message from Customer';
            $body    = 'You have received a new message from customer. Please click on the following link:<br>';
            $link    = url('agent/message/index');
            $body   .= '<a href="'.$link.'">'.$link.'</a>';
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return redirect()->route('message')->with('success', 'Message is created successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply
    الغرض: عرض صفحة محادثة رسالة مع الوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply($id)
    {
        $uid = Auth::id();

        $message = Message::with(['user:id,name,photo','agent:id,name,photo,email'])
            ->where('id', $id)->where('user_id', $uid)->firstOrFail();

        $replies = MessageReply::with(['user:id,name,photo','agent:id,name,photo'])
            ->where('message_id', $id)
            ->orderBy('id')
            ->get();

        $counts = [
            'messages' => Message::where('user_id', $uid)->count(),
            'wishlist' => Wishlist::where('user_id', $uid)->count(),
        ];

        return view('user.message.reply', compact('message','replies','counts'))->with('activeTab','messages');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply_submit
    الغرض: إرسال رد من المستخدم + إشعار بريد للوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply_submit(Request $request, $m_id, $a_id)
    {
        $request->validate(['reply' => 'required|string']);

        $reply = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id    = Auth::id();
        $reply->agent_id   = $a_id;
        $reply->sender     = 'Customer';
        $reply->reply      = $request->reply;
        $reply->save();

        if ($agent = Agent::find($a_id)) {
            $subject = 'New Reply from Customer';
            $body    = 'You have received a new reply from customer. Please click on the following link:<br>';
            $link    = url('agent/message/reply/'.$m_id);
            $body   .= '<a href="'.$link.'">'.$link.'</a>';
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return back()->with('success', 'Reply sent successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_delete
    الغرض: حذف رسالة يملكها المستخدم الحالي
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_delete($id)
    {
        $uid = Auth::id();
        Message::where('id', $id)->where('user_id', $uid)->delete();
        return back()->with('success', 'Message deleted successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: toggle
    الغرض: تبديل حالة عنصر بالمفضلة عبر AJAX (إضافة/حذف)
    ───────────────────────────────────────────────────────────────────────────*/
    public function toggle(Request $request)
    {
        $uid = Auth::id();
        $pid = (int) $request->input('property_id');

        $row = Wishlist::where('user_id',$uid)->where('property_id',$pid)->first();

        if ($row) {
            $row->delete();
            \Cache::forget("wishids:{$uid}");
            return response()->json(['added' => false]);
        } else {
            Wishlist::create(['user_id'=>$uid,'property_id'=>$pid]);
            \Cache::forget("wishids:{$uid}");
            return response()->json(['added' => true]);
        }
    }
}
