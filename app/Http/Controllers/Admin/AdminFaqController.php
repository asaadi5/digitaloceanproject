<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class AdminFaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('id','asc')->get();
        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => ['required'],
            'answer' => ['required'],
        ]);

        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return redirect()->route('admin_faq_index')->with('success', 'FAQ created successfully');
    }

    public function edit($id)
    {
        $faq = Faq::where('id',$id)->first();
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required',
            'answer' => ['required'],
        ]);

        $faq = Faq::where('id',$id)->first();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return redirect()->route('admin_faq_index')->with('success', 'FAQ updated successfully');
    }

    public function delete($id)
    {
        $faq = Faq::where('id',$id)->first();
        $faq->delete();
        
        return redirect()->route('admin_faq_index')->with('success', 'FAQ deleted successfully');
    }
}
