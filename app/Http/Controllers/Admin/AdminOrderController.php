<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('id','desc')->get();
        return view('admin.order.index', compact('orders'));
    }

    public function invoice($id)
    {
        $order = Order::where('id',$id)->first();
        if(!$order){
            return redirect()->back()->with('error', 'Order not found');
        }
        return view('admin.order.invoice', compact('order'));
    }
}
