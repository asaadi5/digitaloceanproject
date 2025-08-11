<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\Agent;
use App\Mail\Websitemail;

class AdminPropertyController extends Controller
{
    public function index()
    {
        $properties = Property::orderBy('id', 'desc')->get();
        return view('admin.property.index', compact('properties'));
    }

    public function detail($id)
    {
        $property = Property::where('id',$id)->first();
        return view('admin.property.detail', compact('property'));
    }

    public function change_status($id)
    {
        $property = Property::where('id',$id)->first();
        if ($property->status == 'Pending') {
            $property->status = 'Active';
        } else {
            $property->status = 'Pending';
        }
        $property->save();

        // Send email to agent
        $link = route('agent_property_index');
        $subject = 'Property Status Updated';
        $message = 'Your property status has been updated to ' . $property->status . '. To check this, please go to this link:<br><a href="' . $link . '">'.$link.'</a>';

        \Mail::to($property->agent->email)->send(new Websitemail($subject,$message));

        return redirect()->back()->with('success', 'Property status updated successfully.');
    }

    public function delete($id)
    {
        $property = Property::where('id',$id)->first();
        if(!$property){
            return redirect()->back()->with('error', 'Property not found');
        }
        if($property->featured_photo != '') {
            unlink(public_path('uploads/'.$property->featured_photo));
        }
        $photos = PropertyPhoto::where('property_id',$property->id)->get();
        foreach($photos as $photo){
            if($photo->photo != '') {
                unlink(public_path('uploads/'.$photo->photo));
            }
        }
        $property->delete();

        return redirect()->back()->with('success', 'Property deleted successfully');
    }
}
