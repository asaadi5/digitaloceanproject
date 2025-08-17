<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Agent;
use App\Models\Wishlist;
use App\Models\Message;
use App\Models\MessageReply;
use App\Mail\Websitemail;

class ]0UserController extends Controller
{
    /* =========================
     *       Protected
     * ========================= */

    // GET /api/v1/dashboard
    public function dashboard(Request $request)
    {
        $uid = $request->user()->id;

        $total_messages       = Message::where('user_id', $uid)->count();
        $total_wishlist_items = Wishlist::where('user_id', $uid)->count();

        return response()->json([
            'total_messages'       => $total_messages,
            'total_wishlist_items' => $total_wishlist_items,
        ]);
    }

    // GET /api/v1/profile
    public function profile(Request $request)
    {
        $u = $request->user();

        return response()->json([
            'id'       => $u->id,
            'name'     => $u->name,
            'username' => $u->username ?? null,
            'email'    => $u->email,
            'photo'    => $u->photo ? url('uploads/'.$u->photo) : null,
        ]);
    }

    // POST /api/v1/profile  (يمكن إرسال photo كـ multipart/form-data)
    public function profile_submit(Request $request)
    {
        $u = $request->user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,'.$u->id,
            'photo'            => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'         => 'sometimes|nullable|string|min:6',
            'confirm_password' => 'required_with:password|same:password',
        ]);

        if ($request->hasFile('photo')) {
            $final_name = 'user_'.time().'.'.$request->photo->extension();
            if ($u->photo) {
                @unlink(public_path('uploads/'.$u->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $u->photo = $final_name;
        }

        if ($request->filled('password')) {
            $u->password = Hash::make($request->password);
        }

        $u->name  = $request->name;
        $u->email = $request->email;
        $u->save();

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي',
            'user'    => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'photo' => $u->photo ? url('uploads/'.$u->photo) : null,
            ]
        ]);
    }

    // GET /api/v1/wishlist
    public function wishlist(Request $request)
    {
        $uid = $request->user()->id;

        $wishlists = Wishlist::where('user_id', $uid)->latest()->get();

        return response()->json([
            'items' => $wishlists->map(fn ($w) => [
                'id'          => $w->id,
                'property_id' => $w->property_id,
                'added_at'    => $w->created_at?->toIso8601String(),
            ]),
        ]);
    }

    // DELETE /api/v1/wishlist/{id}
    public function wishlist_delete(Request $request, $id)
    {
        $uid     = $request->user()->id;
        $deleted = Wishlist::where('id', $id)->where('user_id', $uid)->delete();

        return response()->json([
            'deleted' => (bool) $deleted,
            'message' => $deleted ? 'تم حذف العنصر من المفضلة' : 'العنصر غير موجود',
        ]);
    }

    // GET /api/v1/messages
    public function message(Request $request)
    {
        $uid = $request->user()->id;

        $messages = Message::where('user_id', $uid)->latest()->get();

        return response()->json([
            'items' => $messages->map(fn ($m) => [
                'id'         => $m->id,
                'agent_id'   => $m->agent_id,
                'subject'    => $m->subject,
                'message'    => $m->message,
                'created_at' => $m->created_at?->toIso8601String(),
            ]),
        ]);
    }

    // POST /api/v1/messages
    public function message_store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:500',
            'message'  => 'required|string',
            'agent_id' => 'required|integer|exists:agents,id',
        ]);

        $uid = $request->user()->id;

        $msg             = new Message();
        $msg->user_id    = $uid;
        $msg->agent_id   = $request->agent_id;
        $msg->subject    = $request->subject;
        $msg->message    = $request->message;
        $msg->save();

        // إخطار الوكيل بالبريد (اختياري)
        $subject = 'New Message from Customer';
        $body    = 'You have received a new message from customer. Please click on the following link:<br>';
        $link    = url('agent/message/index');
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        if ($agent = Agent::find($request->agent_id)) {
            if ($agent->email) {
                Mail::to($agent->email)->send(new Websitemail($subject, $body));
            }
        }

        return response()->json([
            'message' => 'تم إنشاء الرسالة بنجاح',
            'item'    => [
                'id'       => $msg->id,
                'agent_id' => $msg->agent_id,
                'subject'  => $msg->subject,
                'message'  => $msg->message,
            ],
        ], 201);
    }

    // GET /api/v1/messages/{id}
    public function message_reply(Request $request, $id)
    {
        $uid     = $request->user()->id;
        $message = Message::where('id', $id)->where('user_id', $uid)->firstOrFail();
        $replies = MessageReply::where('message_id', $id)->latest()->get();

        return response()->json([
            'message' => [
                'id'       => $message->id,
                'agent_id' => $message->agent_id,
                'subject'  => $message->subject,
                'body'     => $message->message,
            ],
            'replies' => $replies->map(fn ($r) => [
                'id'         => $r->id,
                'sender'     => $r->sender,
                'reply'      => $r->reply,
                'created_at' => $r->created_at?->toIso8601String(),
            ]),
        ]);
    }

    // POST /api/v1/messages/{message_id}/reply/{agent_id}
    public function message_reply_submit(Request $request, $m_id, $a_id)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        $uid = $request->user()->id;

        // تأكد أن الرسالة تخص هذا المستخدم
        Message::where('id', $m_id)->where('user_id', $uid)->firstOrFail();

        $reply             = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id    = $uid;
        $reply->agent_id   = $a_id;
        $reply->sender     = 'Customer';
        $reply->reply      = $request->reply;
        $reply->save();

        // إخطار الوكيل بالبريد (اختياري)
        $subject = 'New Reply from Customer';
        $body    = 'You have received a new reply from customer. Please click on the following link:<br>';
        $link    = url('agent/message/reply/'.$m_id);
        $body   .= '<a href="'.$link.'">'.$link.'</a>';

        if ($agent = Agent::find($a_id)) {
            if ($agent->email) {
                Mail::to($agent->email)->send(new Websitemail($subject, $body));
            }
        }

        return response()->json([
            'message' => 'تم إرسال الرد بنجاح',
            'reply'   => [
                'id'         => $reply->id,
                'sender'     => $reply->sender,
                'reply'      => $reply->reply,
                'created_at' => $reply->created_at?->toIso8601String(),
            ],
        ]);
    }

    // DELETE /api/v1/messages/{id}
    public function message_delete(Request $request, $id)
    {
        $uid     = $request->user()->id;
        $deleted = Message::where('id', $id)->where('user_id', $uid)->delete();

        return response()->json([
            'deleted' => (bool) $deleted,
            'message' => $deleted ? 'تم حذف الرسالة' : 'الرسالة غير موجودة',
        ]);
    }

    // POST /api/v1/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    /* =========================
     *          Public
     * ========================= */

    // (اختياري) GET /api/v1/registration
    public function registration()
    {
        return response()->json(['message' => 'استخدم /registration-submit لإتمام التسجيل'], 200);
    }

    // POST /api/v1/registration-submit
    public function registration_submit(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'username'         => 'required|string|max:255|unique:users,username',
            'email'            => 'required|email|max:255|unique:users,email',
            'password'         => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        $verifyToken = hash('sha256', time());

        $user           = new User();
        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->token    = $verifyToken;
        $user->status   = 0; // بانتظار التفعيل
        $user->save();

        // إرسال رابط التفعيل
        $link    = url('registration-verify/'.$verifyToken.'/'.$request->email);
        $subject = 'Registration Verification';
        $body    = 'Click on the following link to verify your email: <br><a href="'.$link.'">'.$link.'</a>';
        Mail::to($request->email)->send(new Websitemail($subject, $body));

        return response()->json(['message' => 'تم التسجيل، تحقق من بريدك لتفعيل الحساب'], 201);
    }

    // GET /api/v1/registration-verify?token=&email=
    public function registration_verify(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'رمز/بريد غير صالح'], 400);
        }

        $user->token  = '';
        $user->status = 1;
        $user->save();

        return response()->json(['message' => 'تم تفعيل البريد، يمكنك تسجيل الدخول الآن']);
    }

    // POST /api/v1/login
    public function login_submit(Request $request)
    {
        $request->validate([
            'email'    => 'required', // بريد أو اسم مستخدم
            'password' => 'required',
        ]);

        $login_input = $request->email;
        $fieldType   = filter_var($login_input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($fieldType, $login_input)
            ->where('status', 1)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $user->createToken('flutter-mobile')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'username' => $user->username ?? null,
            ],
        ]);
    }

    // POST /api/v1/forget-password
    public function forget_password_submit(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        $token      = hash('sha256', time());
        $user->token = $token;
        $user->save();

        // يمكن استبداله بمسار API إن رغبت
        $link    = route('reset_password', [$token, $request->email]);
        $subject = 'Reset Password';
        $body    = 'Click on the following link to reset your password:<br><a href="'.$link.'">'.$link.'</a>';
        Mail::to($request->email)->send(new Websitemail($subject, $body));

        return response()->json(['message' => 'تم إرسال رابط إعادة التعيين إلى بريدك']);
    }

    // POST /api/v1/reset-password  (body: token,email,password,confirm_password)
    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'token'            => 'required',
            'email'            => 'required|email',
            'password'         => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'رمز/بريد غير صالح'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->token    = '';
        $user->save();

        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
    }
}
