<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;

class AdminPackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('id','asc')->get();
        return view('admin.package.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.package.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'allowed_days' => 'required|numeric',
            'allowed_properties' => 'required|numeric',
            'allowed_featured_properties' => 'required|numeric',
            'allowed_photos' => 'required|numeric',
            'allowed_videos' => 'required|numeric',
        ]);

        $package = new Package();
        $package->name = $request->name;
        $package->price = $request->price;
        $package->allowed_days = $request->allowed_days;
        $package->allowed_properties = $request->allowed_properties;
        $package->allowed_featured_properties = $request->allowed_featured_properties;
        $package->allowed_photos = $request->allowed_photos;
        $package->allowed_videos = $request->allowed_videos;
        $package->save();

        return redirect()->route('admin_package_index')->with('success', 'Package created successfully');
    }

    public function edit($id)
    {
        $package = Package::where('id',$id)->first();
        return view('admin.package.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'allowed_days' => 'required|numeric',
            'allowed_properties' => 'required|numeric',
            'allowed_featured_properties' => 'required|numeric',
            'allowed_photos' => 'required|numeric',
            'allowed_videos' => 'required|numeric',
        ]);

        $package = Package::where('id',$id)->first();
        $package->name = $request->name;
        $package->price = $request->price;
        $package->allowed_days = $request->allowed_days;
        $package->allowed_properties = $request->allowed_properties;
        $package->allowed_featured_properties = $request->allowed_featured_properties;
        $package->allowed_photos = $request->allowed_photos;
        $package->allowed_videos = $request->allowed_videos;
        $package->save();

        return redirect()->route('admin_package_index')->with('success', 'Package updated successfully');
    }

    public function delete($id)
    {
        $package = Package::where('id',$id)->first();
        $package->delete();
        
        return redirect()->route('admin_package_index')->with('success', 'Package deleted successfully');
    }
}
