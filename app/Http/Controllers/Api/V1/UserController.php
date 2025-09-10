<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail};
use Illuminate\Validation\Rules\Password;
use App\Mail\Websitemail;
use App\Models\{User, Agent, Wishlist, Message, MessageReply};
use App\Http\Resources\PropertyResource;

class UserController extends Controller
{
    /*───────────────────────────────────────────────────────────────────────────
    register: إنشاء مستخدم + إرسال رابط تفعيل
    ملاحظة: تأكّد أن موديل User يستخدم HasApiTokens لسانكتُم
    ───────────────────────────────────────────────────────────────────────────*/
    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'username'         => 'required|string|max:255|unique:users,username',
            'email'            => 'required|string|max:255|email|unique:users,email',
            'password'         => ['required', Password::min(6)],
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

        // استخدم مسار الـ API المعرّف لديك: /api/v1/auth/verify/{token}/{email}
        $link    = url('api/v1/auth/verify/'.$token.'/'.$request->email); // أو route('api.v1.auth.verify', [$token,$request->email])
        $subject = 'Registration Verification';
        $message = 'انقر على الرابط لتفعيل حسابك: <br><a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return response()->json(['message' => 'Registered. Check your email to verify.'], 201);
    }

    /*───────────────────────────────────────────────────────────────────────────
    verify: تفعيل الحساب عبر التوكن
    ───────────────────────────────────────────────────────────────────────────*/
    public function verify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) return response()->json(['message' => 'Invalid token or email'], 404);

        $user->token  = '';
        $user->status = 1;
        $user->save();

        return response()->json(['message' => 'تم التفعيل. يمكنك الآن تسجيل الدخول.']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    login: يدعم email أو username
    ───────────────────────────────────────────────────────────────────────────*/
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('web')->attempt([$fieldType => $request->email, 'password' => $request->password, 'status' => 1])) {
            $user = Auth::guard('web')->user();

            // ⬇️ إنشاء توكين مع صلاحية "user"
            $token = $user->createToken('flutter-user', ['user'])->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token'  => $token,
                'user'   => $user,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'بيانات الدخول غير صحيحة'], 401);
    }


    /*───────────────────────────────────────────────────────────────────────────*/
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    /*───────────────────────────────────────────────────────────────────────────*/
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
    updateProfile: تحديث البيانات + صورة + كلمة السر (اختياري)
    ───────────────────────────────────────────────────────────────────────────*/
    public function updateProfile(Request $request)
    {
        $u = $request->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$u->id,
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $final = 'user_'.time().'.'.$request->photo->extension();
            if ($u->photo && file_exists(public_path('uploads/'.$u->photo))) {
                @unlink(public_path('uploads/'.$u->photo));
            }
            $request->photo->move(public_path('uploads'), $final);
            $u->photo = $final;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password'         => ['required', Password::min(6)],
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
    forgotPassword: يولّد توكن ويرسل رابط
    (لو عندك صفحة ويب للإعادة استخدم رابطها؛ وإلّا يمكنك تجاهل النقر والاكتفاء بالـ API)
    ───────────────────────────────────────────────────────────────────────────*/
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email not recognized'], 404);
        }

        $token = hash('sha256', time());
        $user->token = $token;
        $user->save();

        // عدّل هذا الرابط لواجهة الويب إن كان لديك صفحة جاهزة
        $link    = url('reset-password/'.$token.'/'.$request->email);
        $subject = 'Reset Password';
        $message = 'انقر على الرابط لإعادة كلمة المرور:<br><a href="'.$link.'">'.$link.'</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));

        return response()->json(['message' => 'Reset link sent']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    resetPassword: يحدّث كلمة السر عبر token/email
    ───────────────────────────────────────────────────────────────────────────*/
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'            => 'required|email',
            'token'            => 'required',
            'password'         => ['required', Password::min(6)],
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
    wishlist: عرض وحذف
    ───────────────────────────────────────────────────────────────────────────*/
    public function wishlistIndex(Request $request)
    {
        $items = Wishlist::with(['property.type','property.location','property.agent'])
            ->where('user_id', $request->user()->id)
            ->latest('id')->get();

        $data = $items->map(function($w){
            return [
                'id'       => $w->id,
                'property' => $w->property ? new PropertyResource($w->property) : null,
            ];
        });

        return response()->json(['wishlist' => $data]);
    }

    public function wishlistDelete(Request $request, $id)
    {
        $own = Wishlist::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$own) return response()->json(['message' => 'Not found'], 404);

        $own->delete();
        return response()->json(['message' => 'Deleted']);
    }

    /*───────────────────────────────────────────────────────────────────────────
    الرسائل: إنشاء/عرض/رد/حذف
    ───────────────────────────────────────────────────────────────────────────*/
    public function messages(Request $request)
    {
        $messages = Message::where('user_id', $request->user()->id)->latest('id')->get();
        return response()->json(['messages' => $messages]);
    }

    public function messageStore(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'message'  => 'required|string|max:5000',
            'agent_id' => 'required|exists:agents,id',
        ]);

        $m = new Message();
        $m->user_id  = $request->user()->id;
        $m->agent_id = $request->agent_id;
        $m->subject  = $request->subject;
        $m->message  = $request->message;
        $m->save();

        $subject = 'New Message from Customer';
        $body    = 'لقد تلقيت رسالة من زبون، تفضّل بالدخول:<br>';
        $link    = url('agent/message/index');
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        if ($agent = Agent::find($request->agent_id)) {
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return response()->json(['message' => 'Message created', 'id' => $m->id], 201);
    }

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

    public function messageReply(Request $request, $id)
    {
        $request->validate(['reply' => 'required|string|max:5000']);

        $m = Message::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        // خُذ agent_id من الرسالة نفسها لتجنّب التلاعب
        $r = new MessageReply();
        $r->message_id = $m->id;
        $r->user_id    = $request->user()->id;
        $r->agent_id   = $m->agent_id;
        $r->sender     = 'Customer';
        $r->reply      = $request->reply;
        $r->save();

        $subject = 'New Reply from Customer';
        $body    = 'You have received a new reply from customer. Please click on the following link:<br>';
        $link    = url('agent/message/reply/'.$m->id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        if ($agent = Agent::find($m->agent_id)) {
            Mail::to($agent->email)->send(new Websitemail($subject, $body));
        }

        return response()->json(['message' => 'Reply sent']);
    }

    public function messageDelete(Request $request, $id)
    {
        $m = Message::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$m) return response()->json(['message' => 'Not found'], 404);

        // احذف الردود ثم الرسالة (لو ما عندك Cascade)
        \App\Models\MessageReply::where('message_id', $m->id)->delete();
        $m->delete();

        return response()->json(['message' => 'تم الحذف']);
    }
}
