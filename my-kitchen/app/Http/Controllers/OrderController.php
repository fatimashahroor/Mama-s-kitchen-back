<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use App\Models\Dish;
use Tymon\JWTAuth\Facades\JWTAuth;
class OrderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:order-list');
         $this->middleware('permission:order-create', ['only' => ['store']]);
         $this->middleware('permission:order-edit', ['only' => ['update']]);
    
    }

    public function getCurrentUserDishes(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $payload = JWTAuth::setToken($token)->getPayload();
        if(!$payload) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $orders = null;
        if($payload->get('role')[0] == "cook") {
            $cook_id = $payload->get('sub');
            $orders = Order::with(['dishes.additional_ingredients' => function ($query) {
                $query->withPivot('quantity'); 
            }])
            ->where('cook_id', $cook_id)
            ->get();
        } else if($payload->get('role')[0] == "customer") {
            $customer_id = $payload->get('sub');
            $orders = Order::with(['dishes.additional_ingredients' => function ($query) {
                $query->withPivot('quantity'); 
            }])
            ->where('user_id', $customer_id)
            ->get();
        }
        $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'status' => $order->status,
                'order_date' => $order->order_date,
                'order_price' => $order->order_price,
                'location_id' => $order->location_id,
                'user_id' => $order->user_id,
                'dishes' => $order->dishes->map(function ($dish) {
                    return [
                        'id' => $dish->id,
                        'name'=> $dish->name,
                        'image_path' => $dish->image_path,
                        'quantity' => $dish->pivot->quantity,
                        'comment' => $dish->pivot->comment,
                        'additional_ingredients' => $dish->additional_ingredients->map(function ($ingredient) {
                            return [
                                'id' => $ingredient->id,
                                'name' => $ingredient->name,
                                'quantity' => $ingredient->pivot->quantity
                            ];
                        })
                    ];
                })
            ];
        });

        return response()->json([$orders]);
    }


    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cook_id' => 'required|exists:users,id|numeric',
            'user_id' => 'required|exists:users,id|numeric',
            'location_id' => 'required|exists:locations,id|numeric',
            'order_price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'required|string',
            'order_date' => 'required|date_format:Y-m-d H:i:s',
            'dishes' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        $order = new Order();
        $order->cook_id = $request->cook_id;
        $order->location_id = $request->location_id;
        $order->status = $request->status;
        $order->order_price = $request->order_price;
        $order->order_date = $request->order_date;
        $order->user_id = $request->user_id;
        $order->save();
        foreach ($request->dishes as $dish) {
            $order->dishes()->attach($dish['dish_id'], ['quantity' => $dish['quantity'], 'comment' => $dish['comment']]);
        }
        $dishModel = Dish::find($dish['dish_id']);
        if ($dishModel && isset($dish['additional_ing'])) {
            foreach ($dish['additional_ings'] as $additional) {
                $dishModel->additional_ingredients()->attach($additional['id'], ['quantity' => $additional['quantity']]);
            }
        }
        if (!$order) {
            return response()->json(['message' => 'Error while creating order'], 400);
        }
        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    public function update ($id, Request $request)
    {
        $validator = Validator::make($request->all(), rules: [
            'status' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->update($request->all());
        return response()->json(['message' => 'Order updated successfully', 'order' => $order], 200);
    }    

    public function show($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }
}



