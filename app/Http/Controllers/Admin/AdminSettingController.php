<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingController extends Controller
{
    public function logo()
    {
        $setting = Setting::where('id',1)->first();
        return view('admin.setting.logo', compact('setting'));
    }

    public function logo_update(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = Setting::where('id',1)->first();
        $final_name = 'logo_'.time().'.'.$request->logo->extension();
        if($setting->logo != '') {
            unlink(public_path('uploads/'.$setting->logo));
        }
        $request->logo->move(public_path('uploads'), $final_name);
        $setting->logo = $final_name;
        $setting->save();

        return redirect()->back()->with('success', 'Logo updated successfully');
    }

    public function favicon()
    {
        $setting = Setting::where('id',1)->first();
        return view('admin.setting.favicon', compact('setting'));
    }

    public function favicon_update(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = Setting::where('id',1)->first();
        $final_name = 'favicon_'.time().'.'.$request->favicon->extension();
        if($setting->favicon != '') {
            unlink(public_path('uploads/'.$setting->favicon));
        }
        $request->favicon->move(public_path('uploads'), $final_name);
        $setting->favicon = $final_name;
        $setting->save();

        return redirect()->back()->with('success', 'Favicon updated successfully');
    }

    public function banner()
    {
        $setting = Setting::where('id',1)->first();
        return view('admin.setting.banner', compact('setting'));
    }

    public function banner_update(Request $request)
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = Setting::where('id',1)->first();
        $final_name = 'banner_'.time().'.'.$request->banner->extension();
        if($setting->banner != '') {
            unlink(public_path('uploads/'.$setting->banner));
        }
        $request->banner->move(public_path('uploads'), $final_name);
        $setting->banner = $final_name;
        $setting->save();

        return redirect()->back()->with('success', 'Banner updated successfully');
    }

    public function footer()
    {
        $setting = Setting::where('id',1)->first();
        return view('admin.setting.footer', compact('setting'));
    }

    public function footer_update(Request $request)
    {
        $request->validate([
            'footer_copyright' => 'required',
        ]);

        $setting = Setting::where('id',1)->first();
        $setting->footer_address = $request->footer_address;
        $setting->footer_phone = $request->footer_phone;
        $setting->footer_email = $request->footer_email;
        $setting->footer_facebook = $request->footer_facebook;
        $setting->footer_twitter = $request->footer_twitter;
        $setting->footer_instagram = $request->footer_instagram;
        $setting->footer_youtube = $request->footer_linkedin;
        $setting->footer_copyright = $request->footer_copyright;
        $setting->save();

        return redirect()->back()->with('success', 'Footer updated successfully');
    }
}
