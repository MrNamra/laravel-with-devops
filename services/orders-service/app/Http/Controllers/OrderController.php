<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\KafkaProducer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $kafkaProducer;

    function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }

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

        $event = [
            'event' => 'order.created',
            'order_id' => (string) $order->id,
            'customer_name' => $order->customer_name,
            'amount' => $order->amount,
            'created_at' => $order->created_at->toIso8601String(),
        ];

        // app(KafkaProducer::class)->publish('order.events', $event);
        $this->kafkaProducer->publish('order.events', $event);

        return response()->json([
            'message' => 'Order created & event published to Kafka',
            'data' => $order
        ], 201);

        return response()->json([
            'message' => 'Order created',
            'data' => $order
        ], 200);
    }
}
