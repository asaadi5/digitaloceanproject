<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Property;
use App\Mail\Websitemail;

class AdminAgentController extends Controller
{
    public function index()
    {
        $agents = Agent::orderBy('id','asc')->get();
        return view('admin.agent.index', compact('agents'));
    }

    public function create()
    {
        return view('admin.agent.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required','unique:agents,email', 'email'],
            'photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
            'company' => ['required'],
            'designation' => ['required'],
            'password' => ['required'],
            'confirm_password' => ['required','same:password'],
        ]);

        $final_name = 'agent_'.time().'.'.$request->photo->extension();
        $request->photo->move(public_path('uploads'), $final_name);

        $agent = new Agent();
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->photo = $final_name;
        $agent->password = bcrypt($request->password);
        $agent->company = $request->company;
        $agent->designation = $request->designation;
        $agent->phone = $request->phone;
        $agent->address = $request->address;
        $agent->country = $request->country;
        $agent->state = $request->state;
        $agent->city = $request->city;
        $agent->zip = $request->zip;
        $agent->facebook = $request->facebook;
        $agent->twitter = $request->twitter;
        $agent->linkedin = $request->linkedin;
        $agent->instagram = $request->instagram;
        $agent->website = $request->website;
        $agent->biography = $request->biography;
        $agent->status = $request->status;
        $agent->save();

        // Send email to agent
        if($request->status == 0) {
            $status = 'Pending';
        } elseif($request->status == 1) {
            $status = 'Active';
        } else {
            $status = 'Suspended';
        }
        $link = route('agent_login');
        $subject = 'Your account is created';
        $message = '<h3>Account Information:</h3>';
        $message .= '<b>Name: </b><br>' . $request->name . '<br><br>';
        $message .= '<b>Email: </b><br>' . $request->email . '<br><br>';
        $message .= '<b>Password: </b><br>' . $request->password . '<br><br>';
        $message .= '<b>Company: </b><br>' . $request->company . '<br><br>';
        $message .= '<b>Designation: </b><br>' . $request->designation . '<br><br>';
        $message .= '<b>Phone: </b><br>' . $request->phone . '<br><br>';
        $message .= '<b>Address: </b><br>' . $request->address . '<br><br>';
        $message .= '<b>Country: </b><br>' . $request->country . '<br><br>';
        $message .= '<b>State: </b><br>' . $request->state . '<br><br>';
        $message .= '<b>City: </b><br>' . $request->city . '<br><br>';
        $message .= '<b>Zip: </b><br>' . $request->zip . '<br><br>';
        $message .= '<b>Facebook: </b><br>' . $request->facebook . '<br><br>';
        $message .= '<b>Twitter: </b><br>' . $request->twitter . '<br><br>';
        $message .= '<b>LinkedIn: </b><br>' . $request->linkedin . '<br><br>';
        $message .= '<b>Instagram: </b><br>' . $request->instagram . '<br><br>';
        $message .= '<b>Website: </b><br>' . $request->website . '<br><br>';
        $message .= '<b>Biography: </b><br>' . $request->biography . '<br><br>';
        $message .= '<b>Status: </b><br>' . $status . '<br><br>';
        $message .= 'Please go to login page:<br><a href="' . $link . '">' . $link . '</a><br><br>';
        $message .= 'Best Regards<br>';
        $message .= 'Admin<br>';

        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        return redirect()->route('admin_agent_index')->with('success', 'Agent created successfully');
    }

    public function edit($id)
    {
        $agent = Agent::where('id',$id)->first();
        return view('admin.agent.edit', compact('agent'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required','email','unique:agents,email,'.$id],
            'company' => ['required'],
            'designation' => ['required'],
        ]);

        $agent = Agent::where('id',$id)->first();

        if($request->hasFile('photo')){
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $final_name = 'agent_'.time().'.'.$request->photo->extension();
            if($agent->photo != '') {
                unlink(public_path('uploads/'.$agent->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $agent->photo = $final_name;
        }

        if($request->password != '') {
            $request->validate([
                'password' => ['required'],
                'confirm_password' => ['required','same:password'],
            ]);
            $agent->password = bcrypt($request->password);
        }

        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->company = $request->company;
        $agent->designation = $request->designation;
        $agent->phone = $request->phone;
        $agent->address = $request->address;
        $agent->country = $request->country;
        $agent->state = $request->state;
        $agent->city = $request->city;
        $agent->zip = $request->zip;
        $agent->facebook = $request->facebook;
        $agent->twitter = $request->twitter;
        $agent->linkedin = $request->linkedin;
        $agent->instagram = $request->instagram;
        $agent->website = $request->website;
        $agent->biography = $request->biography;
        $agent->status = $request->status;
        $agent->save();

        return redirect()->route('admin_agent_index')->with('success', 'Agent updated successfully');
    }

    public function delete($id)
    {
        // If agent has any property associated with it, you can not delete this agent
        $property = Property::where('agent_id',$id)->first();
        if($property) {
            return redirect()->route('admin_agent_index')->with('error', 'Agent cannot be deleted as it is associated with a property');
        }

        $agent = Agent::where('id',$id)->first();
        if($agent->photo != '') {
            unlink(public_path('uploads/'.$agent->photo));
        }
        $agent->delete();
        
        return redirect()->route('admin_agent_index')->with('success', 'Agent deleted successfully');
    }
}
