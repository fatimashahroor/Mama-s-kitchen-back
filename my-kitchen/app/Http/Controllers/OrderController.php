<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:order-list');
         $this->middleware('permission:order-create', ['only' => ['store']]);
         $this->middleware('permission:order-edit', ['only' => ['update']]);
    
    }

    public function index()
    {
        $orders= order::latest()->paginate(5);
        if ($orders['total'] == 0) {
            return response()->json(['message' => 'No orders found'], 404);
        }
        return response()->json($orders);
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|numeric',
            'location_id' => 'required|exists:locations,id|numeric',
            'order_price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        $order = Order::create($request->all());
        if (!$order) {
            return response()->json(['message' => 'Error while creating order'], 400);
        }
        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }
}
