<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            'data' => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required',
            'amount' => 'required|numeric'
        ]);

        $order = Order::create([
            'customer_name' => $data['customer_name'],
            'amount' => $data['amount'],
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Order created',
            'data' => $order
        ], 200);
    }
}
