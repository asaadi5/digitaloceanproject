<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\Models\User;
use App\Models\Agent;
use App\Models\Wishlist;
use App\Models\Message;
use App\Models\MessageReply;
use App\Mail\Websitemail;

class UserController extends Controller
{
    public function dashboard()
    {
        $total_messages = Message::where('user_id', Auth::guard('web')->user()->id)->count();
        $total_wishlist_items = Wishlist::where('user_id', Auth::guard('web')->user()->id)->count();
        return view('user.dashboard.index', compact('total_messages', 'total_wishlist_items'));
    }

    public function registration()
    {
        return view('user.auth.registration');
    }

    public function registration_submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|max:255|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $token = hash('sha256', time());

        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->token = $token;
        $user->save();

        $link = url('registration-verify/'.$token.'/'.$request->email);
        $subject = 'Registration Verification';
        $message = 'Click on the following link to verify your email: <br><a href="' . $link . '">' . $link . '</a>';

        \Mail::to($request->email)->send(new Websitemail($subject, $message));
        return redirect()->back()->with('success', 'Registration successful. Please check your email to verify your account.');
    }

    public function registration_verify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid token or email');
        }
        $user->token = '';
        $user->status = 1;
        $user->update();

        return redirect()->route('login')->with('success', 'Email verified successfully. You can now login.');
    }

    public function login()
    {
        return view('user.auth.login');
    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $login_input = $request->email;

        // تحديد هل المستخدم أدخل بريد إلكتروني أو اسم مستخدم
        $fieldType = filter_var($login_input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $login_input,
            'password' => $request->password,
            'status' => 1,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
        } else {
            return redirect()->back()->with('error', 'بيانات الدخول غير صحيحة');
        }
    }


    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    public function forget_password()
    {
        return view('user.auth.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return redirect()->back()->with('error', 'Email not found');
        }

        $token = hash('sha256', time());
        $user->token = $token;
        $user->update();

        $link = route('reset_password', [$token,$request->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to reset your password: <br>';
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        \Mail::to($request->email)->send(new Websitemail($subject,$message));

        return redirect()->back()->with('success', 'Reset password link sent to your email');

    }

    public function reset_password($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if(!$user){
            return redirect()->route('login')->with('error', 'Invalid token or email');
        }
        return view('user.auth.reset_password', compact('token', 'email'));
    }

    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::where('email', $email)->where('token', $token)->first();
        $user->password = Hash::make($request->password);
        $user->token = '';
        $user->update();

        return redirect()->route('login')->with('success', 'Password reset successfully');
    }

    public function profile()
    {
        return view('user.profile.index');
    }

    public function profile_submit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.Auth::guard('web')->user()->id,
        ]);

        $user = User::where('id',Auth::guard('web')->user()->id)->first();

        if($request->photo){
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $final_name = 'user_'.time().'.'.$request->photo->extension();
            if($user->photo != '') {
                unlink(public_path('uploads/'.$user->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $user->photo = $final_name;
        }

        if($request->password){
            $request->validate([
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->update();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function wishlist()
    {
        $wishlists = Wishlist::where('user_id', Auth::guard('web')->user()->id)->get();
        return view('user.wishlist.index', compact('wishlists'));
    }

    public function wishlist_delete($id)
    {
        Wishlist::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Wishlist item deleted successfully');
    }

    public function message()
    {
        $messages = Message::where('user_id', Auth::guard('web')->user()->id)->get();
        return view('user.message.index', compact('messages'));
    }

    public function message_create()
    {
        $agents = Agent::where('status', 1)->get();
        return view('user.message.create', compact('agents'));
    }

    public function message_store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'agent_id' => 'required',
        ]);

        $message = new Message();
        $message->user_id = Auth::guard('web')->user()->id;
        $message->agent_id = $request->agent_id;
        $message->subject = $request->subject;
        $message->message = $request->message;
        $message->save();

        // Send email to agent
        $subject = 'New Message from Customer';
        $message = 'You have received a new message from customer. Please click on the following link:<br>';
        $link = url('agent/message/index');
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        $agent = Agent::where('id', $request->agent_id)->first();

        \Mail::to($agent->email)->send(new Websitemail($subject, $message));

        return redirect()->route('message')->with('success', 'Message is created successfully');
    }

    public function message_reply($id)
    {
        $message = Message::where('id', $id)->first();
        $replies = MessageReply::where('message_id', $id)->get();
        return view('user.message.reply', compact('message', 'replies'));
    }

    public function message_reply_submit(Request $request, $m_id, $a_id)
    {
        $request->validate([
            'reply' => 'required',
        ]);

        $reply = new MessageReply();
        $reply->message_id = $m_id;
        $reply->user_id = Auth::guard('web')->user()->id;
        $reply->agent_id = $a_id;
        $reply->sender = 'Customer';
        $reply->reply = $request->reply;
        $reply->save();

        // Send email to agent
        $subject = 'New Reply from Customer';
        $message = 'You have received a new reply from customer. Please click on the following link:<br>';
        $link = url('agent/message/reply/'.$m_id);
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        $agent = Agent::where('id', $a_id)->first();

        \Mail::to($agent->email)->send(new Websitemail($subject, $message));

        return redirect()->back()->with('success', 'Reply sent successfully');
    }

    public function message_delete($id)
    {
        Message::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Message deleted successfully');
    }
}
