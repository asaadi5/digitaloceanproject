<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Admin;
use App\Models\Package;
use App\Models\Order;
use App\Models\Property;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Agent;
use App\Mail\Websitemail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $total_packages = Package::count();
        $total_completed_orders = Order::where('status', 'Completed')->count();
        $total_active_properties = Order::where('status', 'Active')->count();
        $total_active_subscribers = Subscriber::where('status', 1)->count();
        $total_active_customers = User::where('status', 1)->count();
        $total_active_agents = Agent::where('status', 1)->count();
        return view('admin.dashboard.index', compact('total_packages', 'total_completed_orders', 'total_active_properties', 'total_active_subscribers', 'total_active_customers', 'total_active_agents'));
    }

    public function login()
    {
        return view('admin.auth.login');
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
        ];

        if (Auth::guard('admin')->attempt($data)) {
            return redirect()->route('admin_dashboard')->with('success', 'Logged in successfully');
        } else {
            return redirect()->back()->with('error', 'Invalid credentials');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin_login')->with('success', 'Logged out successfully');
    }

    public function forget_password()
    {
        return view('admin.auth.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Email not found');
        }

        $token = hash('sha256', time());
        $admin->token = $token;
        $admin->update();

        $link = route('admin_reset_password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to reset your password: <br>';
        $message .= '<a href="' . $link . '">' . $link . '</a>';

        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        return redirect()->back()->with('success', 'Reset password link sent to your email');

    }

    public function reset_password($token, $email)
    {
        $admin = Admin::where('email', $email)->where('token', $token)->first();
        if (!$admin) {
            return redirect()->route('admin_login')->with('error', 'Invalid token or email');
        }
        return view('admin.auth.reset_password', compact('token', 'email'));
    }

    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $admin = Admin::where('email', $email)->where('token', $token)->first();
        $admin->password = Hash::make($request->password);
        $admin->token = '';
        $admin->update();

        return redirect()->route('admin_login')->with('success', 'Password reset successfully');
    }

    public function profile()
    {
        return view('admin.profile.index');
    }

    public function profile_submit(Request $request)
    {
        $admin = Admin::findOrFail(Auth::guard('admin')->user()->id);

        // التحقق الجزئي
        $rules = [];

        if ($request->has('name') && $request->name != '') {
            $rules['name'] = 'string|max:255';
        }

        if ($request->has('email') && $request->email != '') {
            $rules['email'] = 'email|unique:admins,email,' . $admin->id;
        }

        if ($request->hasFile('photo')) {
            $rules['photo'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6';
            $rules['confirm_password'] = 'required|same:password';
        }

        $request->validate($rules);

        // تحديث الصورة
        if ($request->hasFile('photo')) {
            $final_name = 'admin_' . time() . '.' . $request->photo->extension();
            if ($admin->photo && file_exists(public_path('uploads/' . $admin->photo))) {
                unlink(public_path('uploads/' . $admin->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $admin->photo = $final_name;
        }

        // تحديث كلمة المرور
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        // تحديث الاسم والإيميل فقط إذا أُرسلت
        if ($request->filled('name')) {
            $admin->name = $request->name;
        }

        if ($request->filled('email')) {
            $admin->email = $request->email;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
