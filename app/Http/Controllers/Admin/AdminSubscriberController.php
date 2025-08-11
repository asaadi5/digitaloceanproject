<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;

class AdminSubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::orderBy('id','asc')->get();
        return view('admin.subscriber.index', compact('subscribers'));
    }

    public function create()
    {
        return view('admin.subscriber.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:subscribers,email'],
            'status' => ['required'],
        ]);

        $subscriber = new Subscriber();
        $subscriber->email = $request->email;
        $subscriber->status = $request->status;
        $subscriber->save();

        return redirect()->route('admin_subscriber_index')->with('success', 'Subscriber created successfully');
    }

    public function edit($id)
    {
        $subscriber = Subscriber::where('id',$id)->first();
        return view('admin.subscriber.edit', compact('subscriber'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:subscribers,email,'.$id],
            'status' => ['required'],
        ]);

        $subscriber = Subscriber::where('id',$id)->first();
        $subscriber->email = $request->email;
        $subscriber->status = $request->status;
        $subscriber->save();

        return redirect()->route('admin_subscriber_index')->with('success', 'Subscriber updated successfully');
    }

    public function delete($id)
    {
        $subscriber = Subscriber::where('id',$id)->first();
        $subscriber->delete();
        
        return redirect()->route('admin_subscriber_index')->with('success', 'Subscriber deleted successfully');
    }

    public function export()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=subscribers_'.date('Y-m-d_H-i-s').'.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['SL', 'Email']);
        
        $subscribers = Subscriber::where('status', 1)->orderBy('id', 'ASC')->get();
        
        $i = 0;
        foreach ($subscribers as $row) {
            $i++;
            fputcsv($output, [$i, $row->email]);
        }
        
        fclose($output);
        exit;
    }
}
