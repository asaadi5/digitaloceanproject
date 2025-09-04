<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail};
use App\Mail\Websitemail;
use App\Models\{User, Agent, Wishlist, Message, MessageReply};
use App\Http\Resources\PropertyResource;

class UserController extends Controller
{
    /*───────────────────────────────────────────────────────────────────────────
    الدالة: register
    الغرض: تسجيل مستخدم جديد وإرسال إيميل تحقق (JSON)
    المدخلات: name, username, email, password, confirm_password
    ───────────────────────────────────────────────────────────────────────────*/
    public function register(Request $request)
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
        $message = 'انقر على الرابط لإعادة كلمة السر <br><a href="' . $link . '">' . $link . '</a>';
        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return response()->json(['message' => 'Registered. Check your email to verify.'], 201);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: verify
    الغرض: تفعيل المستخدم عبر رابط التحقق (JSON)
    المسار: GET /api/v1/auth/verify/{token}/{email}
    ───────────────────────────────────────────────────────────────────────────*/
    public function verify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) return response()->json(['message' => 'Invalid token or email'], 404);

        $user->token = '';
        $user->status = 1;
        $user->save();

        return response()->json(['message' => 'تم التفعيل. يمكنك الآن تسجيل الدخول.']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: login
    الغرض: تسجيل الدخول وإرجاع توكن Sanctum (يدعم email أو username)
    المدخلات: login (email or username), password
    ───────────────────────────────────────────────────────────────────────────*/
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required', // email or username
            'password' => 'required',
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $login)->where('status', 1)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('flutter')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'photo'    => $user->photo ? url('uploads/'.$user->photo) : null,
            ]
        ]);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: logout
    الغرض: حذف التوكن الحالي (Sanctum)
    ───────────────────────────────────────────────────────────────────────────*/
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: me
    الغرض: جلب ملف المستخدم (JSON)
    ───────────────────────────────────────────────────────────────────────────*/
    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id'       => $u->id,
            'name'     => $u->name,
            'username' => $u->username,
            'email'    => $u->email,
            'photo'    => $u->photo ? url('uploads/'.$u->photo) : null,
        ]);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: updateProfile
    الغرض: تحديث الاسم/البريد/الصورة/كلمة السر (Multipart مدعوم)
    المدخلات: name,email,photo?,password?,confirm_password?
    ───────────────────────────────────────────────────────────────────────────*/
    public function updateProfile(Request $request)
    {
        $u = $request->user();

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$u->id,
        ]);

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $final = 'user_'.time().'.'.$request->photo->extension();
            if ($u->photo && file_exists(public_path('uploads/'.$u->photo))) {
                @unlink(public_path('uploads/'.$u->photo));
            }
            $request->photo->move(public_path('uploads'), $final);
            $u->photo = $final;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password'         => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $u->password = Hash::make($request->password);
        }

        $u->name  = $request->name;
        $u->email = $request->email;
        $u->save();

        return response()->json(['message' => 'Profile updated']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: forgotPassword
    الغرض: إصدار رابط إعادة تعيين كلمة المرور وإرساله بالبريد
    ───────────────────────────────────────────────────────────────────────────*/
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user){
            return response()->json(['message' => 'Email not recognized'], 404);
        }

        $token = hash('sha256', time());
        $user->token = $token;
        $user->save();

        $link    = route('reset_password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'انقر على الرابط لإعادة كلمة المرور <br><a href="'.$link.'">'.$link.'</a>';
        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return response()->json(['message' => 'Reset link sent']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: resetPassword
    الغرض: تعيين كلمة مرور جديدة عبر token/email (JSON)
    المدخلات: email, token, password, confirm_password
    ───────────────────────────────────────────────────────────────────────────*/
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'            => 'required|email',
            'token'            => 'required',
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::where('email', $request->email)->where('token', $request->token)->first();
        if (!$user) return response()->json(['message' => 'Invalid token or email'], 404);

        $user->password = Hash::make($request->password);
        $user->token    = '';
        $user->save();

        return response()->json(['message' => 'تم إعادة تعيين كلمة السر بنجاح']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: wishlistIndex
    الغرض: جلب قائمة المفضلة للمستخدم (مع خصائصها)
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlistIndex(Request $request)
    {
        $items = Wishlist::with(['property.type','property.location','property.agent'])
            ->where('user_id', $request->user()->id)
            ->latest('id')->get();

        // Map to property resources if exists
        $data = $items->map(function($w){
            return [
                'id'       => $w->id,
                'property' => $w->property ? new PropertyResource($w->property) : null,
            ];
        });

        return response()->json(['wishlist' => $data]);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: wishlistDelete
    الغرض: حذف عنصر من المفضلة (JSON)
    المدخلات: $id (Wishlist ID)
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlistDelete(Request $request, $id)
    {
        $own = Wishlist::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$own) return response()->json(['message' => 'Not found'], 404);

        $own->delete();
        return response()->json(['message' => 'Deleted']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: messages
    الغرض: قائمة رسائل المستخدم (JSON)
    ───────────────────────────────────────────────────────────────────────────*/
    public function messages(Request $request)
    {
        $messages = Message::where('user_id', $request->user()->id)->latest('id')->get();
        return response()->json(['messages' => $messages]);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: messageStore
    الغرض: إنشاء رسالة جديدة لوكيل وإشعاره بالبريد
    المدخلات: subject, message, agent_id
    ───────────────────────────────────────────────────────────────────────────*/
    public function messageStore(Request $request)
    {
        $request->validate([
            'subject'  => 'required',
            'message'  => 'required',
            'agent_id' => 'required',
        ]);

        $m = new Message();
        $m->user_id  = $request->user()->id;
        $m->agent_id = $request->agent_id;
        $m->subject  = $request->subject;
        $m->message  = $request->message;
        $m->save();

        $subject = 'New Message from Customer';
        $body    = 'لقد تلقيت رسالة من زبون انقر على الرابط لو سمحت<br>';
        $link    = url('agent/message/index');
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        $agent = Agent::find($request->agent_id);
        if ($agent) {
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return response()->json(['message' => 'Message created', 'id' => $m->id], 201);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: messageShow
    الغرض: تفاصيل رسالة + جميع الردود (JSON)
    المدخلات: $id
    ───────────────────────────────────────────────────────────────────────────*/
    public function messageShow(Request $request, $id)
    {
        $m = Message::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        $replies = MessageReply::where('message_id', $id)->latest('id')->get();

        return response()->json([
            'message' => $m,
            'replies' => $replies,
        ]);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: messageReply
    الغرض: إضافة رد جديد على رسالة وإشعار الوكيل
    المدخلات: reply (text), $id (message_id), agent_id (path or body)
    ───────────────────────────────────────────────────────────────────────────*/
    public function messageReply(Request $request, $id)
    {
        $request->validate(['reply' => 'required', 'agent_id' => 'required']);

        $m = Message::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        $r = new MessageReply();
        $r->message_id = $m->id;
        $r->user_id    = $request->user()->id;
        $r->agent_id   = $request->agent_id;
        $r->sender     = 'Customer';
        $r->reply      = $request->reply;
        $r->save();

        $subject = 'New Reply from Customer';
        $body    = 'You have received a new reply from customer. Please click on the following link:<br>';
        $link    = url('agent/message/reply/'.$m->id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        $agent = Agent::find($request->agent_id);
        if ($agent) {
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return response()->json(['message' => 'Reply sent']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الدالة: messageDelete
    الغرض: حذف رسالة مملوكة للمستخدم
    المدخلات: $id
    ───────────────────────────────────────────────────────────────────────────*/
    public function messageDelete(Request $request, $id)
    {
        $m = Message::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        $m->delete();
        return response()->json(['message' => 'تم الحذف']);
    }
}
