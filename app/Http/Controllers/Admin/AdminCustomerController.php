<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\Websitemail;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = User::orderBy('id','asc')->get();
        return view('admin.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required','unique:users,email', 'email'],
            'photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
            'password' => ['required'],
            'confirm_password' => ['required','same:password'],
        ]);

        $final_name = 'user_'.time().'.'.$request->photo->extension();
        $request->photo->move(public_path('uploads'), $final_name);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->photo = $final_name;
        $user->password = bcrypt($request->password);
        $user->status = $request->status;
        $user->save();

        // Send email to customer
        if($request->status == 0) {
            $status = 'Pending';
        } elseif($request->status == 1) {
            $status = 'Active';
        } else {
            $status = 'Suspended';
        }
        $link = route('login');
        $subject = 'Your account is created';
        $message = '<h3>Account Information:</h3>';
        $message .= '<b>Name: </b><br>' . $request->name . '<br><br>';
        $message .= '<b>Email: </b><br>' . $request->email . '<br><br>';
        $message .= '<b>Password: </b><br>' . $request->password . '<br><br>';
        $message .= '<b>Status: </b><br>' . $status . '<br><br>';
        $message .= 'Please go to login page:<br><a href="' . $link . '">' . $link . '</a><br><br>';
        $message .= 'Best Regards<br>';
        $message .= 'Admin<br>';

        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        return redirect()->route('admin_customer_index')->with('success', 'Customer created successfully');
    }

    public function edit($id)
    {
        $customer = User::where('id',$id)->first();
        return view('admin.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required','email','unique:users,email,'.$id],
        ]);

        $user = User::where('id',$id)->first();

        if($request->hasFile('photo')){
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

        if($request->password != '') {
            $request->validate([
                'password' => ['required'],
                'confirm_password' => ['required','same:password'],
            ]);
            $user->password = bcrypt($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->save();

        return redirect()->route('admin_customer_index')->with('success', 'Customer updated successfully');
    }

    public function delete($id)
    {
        $user = User::where('id',$id)->first();
        if($user->photo != '') {
            unlink(public_path('uploads/'.$user->photo));
        }
        $user->delete();
        
        return redirect()->route('admin_customer_index')->with('success', 'Customer deleted successfully');
    }
}
