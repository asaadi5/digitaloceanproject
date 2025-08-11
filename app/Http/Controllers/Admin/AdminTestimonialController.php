<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class AdminTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('id','asc')->get();
        return view('admin.testimonial.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonial.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'designation' => ['required'],
            'comment' => ['required'],
            'photo' => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
        ]);

        $final_name = 'testimonial_'.time().'.'.$request->photo->extension();
        $request->photo->move(public_path('uploads'), $final_name);

        $testimonial = new Testimonial();
        $testimonial->name = $request->name;
        $testimonial->designation = $request->designation;
        $testimonial->comment = $request->comment;
        $testimonial->photo = $final_name;
        $testimonial->save();

        return redirect()->route('admin_testimonial_index')->with('success', 'Testimonial created successfully');
    }

    public function edit($id)
    {
        $testimonial = Testimonial::where('id',$id)->first();
        return view('admin.testimonial.edit', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'designation' => ['required'],
            'comment' => ['required'],
        ]);

        $testimonial = Testimonial::where('id',$id)->first();

        if($request->hasFile('photo')){
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $final_name = 'testimonial_'.time().'.'.$request->photo->extension();
            if($testimonial->photo != '') {
                unlink(public_path('uploads/'.$testimonial->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $testimonial->photo = $final_name;
        }

        $testimonial->name = $request->name;
        $testimonial->designation = $request->designation;
        $testimonial->comment = $request->comment;
        $testimonial->save();

        return redirect()->route('admin_testimonial_index')->with('success', 'Testimonial updated successfully');
    }

    public function delete($id)
    {
        $testimonial = Testimonial::where('id',$id)->first();
        if($testimonial->photo != '') {
            unlink(public_path('uploads/'.$testimonial->photo));
        }
        $testimonial->delete();
        
        return redirect()->route('admin_testimonial_index')->with('success', 'Testimonial deleted successfully');
    }
}
