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
    المدخلات: —
    المخرجات: View 'user.dashboard.index'
    ───────────────────────────────────────────────────────────────────────────*/
    public function dashboard()
    {
        $uid = Auth::guard('web')->user()->id; // reuse user id
        $total_messages = Message::where('user_id', $uid)->count();
        $total_wishlist_items = Wishlist::where('user_id', $uid)->count();

        return view('user.dashboard.index', compact('total_messages', 'total_wishlist_items'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration
    الغرض: عرض صفحة تسجيل مستخدم جديد
    المدخلات: —
    المخرجات: View 'user.auth.registration'
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration()
    {
        return view('user.auth.registration');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration_submit
    الغرض: التحقق من التسجيل وإنشاء مستخدم وإرسال رابط التحقق
    المدخلات: Request(name,username,email,password,confirm_password)
    المخرجات: Redirect back برسالة نجاح/خطأ
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration_submit(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'username'        => 'required|string|max:255|unique:users,username',
            'email'           => 'required|max:255|email|unique:users,email',
            'password'        => 'required',
            'confirm_password'=> 'required|same:password',
        ]);

        // Create verification token
        $token = hash('sha256', time());

        // Create user (status stays default until verification)
        $user = new User();
        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->token    = $token;
        $user->save();

        // Send verification email
        $link    = url('registration-verify/'.$token.'/'.$request->email);
        $subject = 'Registration Verification';
        $message = 'انقر على الرابط لإعادة كلمة السر <br><a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return back()->with('success', 'تم التسجيل بنجاح. تحقق من بريدك الإلكتروني للتحقق من حسابك.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: registration_verify
    الغرض: تفعيل الحساب بواسطة رابط التحقق
    المدخلات: $token, $email
    المخرجات: Redirect إلى login برسالة مناسبة
    ───────────────────────────────────────────────────────────────────────────*/
    public function registration_verify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'بريد إلكتروني أو رمز غير صالح');
        }

        // Activate account
        $user->token  = '';
        $user->status = 1;
        $user->update();

        return redirect()->route('login')->with('success', 'تم التحقق من حسابك. يمكنك الآن تسجيل الدخول.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login
    الغرض: عرض صفحة تسجيل الدخول
    ───────────────────────────────────────────────────────────────────────────*/
    public function login()
    {
        return view('user.auth.login');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login_submit
    الغرض: محاولة تسجيل الدخول (بالبريد أو اليوزرنيم) للمستخدم النشط
    المدخلات: Request(email|username,password)
    المخرجات: Redirect إلى dashboard أو back بخطأ
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
            'status'   => 1, // must be verified/active
        ];

        // Attempt login with session guard
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
    المدخلات: Request(email)
    المخرجات: Redirect back برسالة نجاح/خطأ
    ───────────────────────────────────────────────────────────────────────────*/
    public function forget_password_submit(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user){
            return back()->with('error', 'لم يتم التعرف على البريد الإلكتروني ');
        }

        // Issue reset token
        $token      = hash('sha256', time());
        $user->token = $token;
        $user->update();

        $link    = route('reset_password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'انقر على الرابط لإعادة كلمة المرور <br><a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject,$message));

        return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: reset_password
    الغرض: عرض صفحة تعيين كلمة مرور جديدة بعد التحقق من الرموز
    المدخلات: $token, $email
    المخرجات: View reset أو Redirect بخطأ
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
    المدخلات: Request(password,confirm_password), $token, $email
    المخرجات: Redirect إلى login مع نجاح
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

        // Update password and clear token
        $user->password = Hash::make($request->password);
        $user->token    = '';
        $user->update();

        return redirect()->route('login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile
    الغرض: عرض صفحة الملف الشخصي
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile()
    {
        return view('user.profile.index');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: profile_submit
    الغرض: تحديث بيانات المستخدم (اسم/بريد/صورة/كلمة مرور)
    المدخلات: Request(name,email,photo?,password?,confirm_password?)
    المخرجات: Redirect back برسالة نجاح
    ───────────────────────────────────────────────────────────────────────────*/
    public function profile_submit(Request $request)
    {
        $uid  = Auth::guard('web')->user()->id;
        $user = User::findOrFail($uid);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$uid,
        ]);

        // Handle photo (optional)
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $final_name = 'user_'.time().'.'.$request->photo->extension();

            // Delete old photo if exists (safe)
            if ($user->photo && file_exists(public_path('uploads/'.$user->photo))) {
                @unlink(public_path('uploads/'.$user->photo));
            }

            // Move uploaded file to public/uploads
            $request->photo->move(public_path('uploads'), $final_name);
            $user->photo = $final_name;
        }

        // Handle password (optional)
        if ($request->filled('password')) {
            $request->validate([
                'password'         => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: wishlist
    الغرض: عرض قائمة المفضلة للمستخدم الحالي
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlist()
    {
        $wishlists = Wishlist::where('user_id', Auth::guard('web')->user()->id)->get();
        return view('user.wishlist.index', compact('wishlists'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: wishlist_delete
    الغرض: حذف عنصر من المفضلة
    المدخلات: $id (Wishlist ID)
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlist_delete($id)
    {
        Wishlist::where('id', $id)->delete();
        return back()->with('success', 'Wishlist item deleted successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message
    الغرض: عرض قائمة رسائل المستخدم
    ───────────────────────────────────────────────────────────────────────────*/
    public function message()
    {
        $messages = Message::where('user_id', Auth::guard('web')->user()->id)->get();
        return view('user.message.index', compact('messages'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_create
    الغرض: عرض نموذج إنشاء رسالة جديدة لوكيل
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_create()
    {
        $agents = Agent::where('status', 1)->get();
        return view('user.message.create', compact('agents'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_store
    الغرض: حفظ رسالة جديدة وإعلام الوكيل بالبريد
    المدخلات: Request(subject,message,agent_id)
    المخرجات: Redirect route('message') مع نجاح
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_store(Request $request)
    {
        $request->validate([
            'subject'  => 'required',
            'message'  => 'required',
            'agent_id' => 'required',
        ]);

        $message = new Message();
        $message->user_id  = Auth::guard('web')->user()->id;
        $message->agent_id = $request->agent_id;
        $message->subject  = $request->subject;
        $message->message  = $request->message;
        $message->save();

        // Notify agent by email
        $subject = 'New Message from Customer';
        $body    = 'You have received a new message from customer. Please click on the following link:<br>';
        $link    = url('agent/message/index');
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        $agent = Agent::where('id', $request->agent_id)->first();
        if ($agent) {
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return redirect()->route('message')->with('success', 'Message is created successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply
    الغرض: عرض صفحة الردود لرسالة معينة
    المدخلات: $id (Message ID)
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply($id)
    {
        $message = Message::where('id', $id)->first();
        $replies = MessageReply::where('message_id', $id)->get();
        return view('user.message.reply', compact('message', 'replies'));
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_reply_submit
    الغرض: إرسال رد من المستخدم وإعلام الوكيل بالبريد
    المدخلات: Request(reply), $m_id (Message ID), $a_id (Agent ID)
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_reply_submit(Request $request, $m_id, $a_id)
    {
        $request->validate(['reply' => 'required']);

        $reply = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id    = Auth::guard('web')->user()->id;
        $reply->agent_id   = $a_id;
        $reply->sender     = 'Customer';
        $reply->reply      = $request->reply;
        $reply->save();

        // Notify agent
        $subject = 'New Reply from Customer';
        $body    = 'You have received a new reply from customer. Please click on the following link:<br>';
        $link    = url('agent/message/reply/'.$m_id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        $agent = Agent::where('id', $a_id)->first();
        if ($agent) {
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return back()->with('success', 'Reply sent successfully');
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: message_delete
    الغرض: حذف رسالة للمستخدم
    المدخلات: $id (Message ID)
    ───────────────────────────────────────────────────────────────────────────*/
    public function message_delete($id)
    {
        Message::where('id', $id)->delete();
        return back()->with('success', 'Message deleted successfully');
    }
}
