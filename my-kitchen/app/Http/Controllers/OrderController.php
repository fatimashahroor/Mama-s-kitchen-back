<?php

namespace App\Http\Controllers;

use App\Models\OrderDishAdditional;
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
            $orders = Order::select('orders.id as order_id', 'orders.status as order_status', 'orders.order_date', 'orders.order_price', 'orders.location_id', 'orders.user_id as customer_id', 'dishes.id as dish_id', 'dishes.name as dish_name', 'dishes.image_path as dish_image',
             'od.quantity as dish_quantity', 'additional_ings.id as additional_ing_id', 'additional_ings.name as additional_ing_name', 'oadi.quantity as additional_ing_quantity', 'od.comment as dish_comment')
            ->join('orders_dishes as od', 'od.order_id', '=', 'orders.id')
            ->join('dishes', 'dishes.id', '=', 'od.dish_id')
            ->leftJoin('orders_dishes_additional_ings as oadi', function ($join) {
                $join->on('oadi.dish_id', '=', 'dishes.id')
                     ->on('oadi.order_id', '=', 'orders.id');
            })
            ->leftJoin('additional_ings', 'additional_ings.id', '=', 'oadi.additional_ing_id')
            ->where('orders.cook_id', $cook_id)
            ->get();
 
        } else if($payload->get('role')[0] == "customer") {
            $customer_id = $payload->get('sub');
            $orders = Order::select('orders.id as order_id', 'orders.status as order_status', 'orders.order_date', 'orders.order_price', 'orders.location_id', 'orders.user_id as customer_id', 'dishes.id as dish_id', 'dishes.name as dish_name', 'dishes.image_path as dish_image',
             'od.quantity as dish_quantity', 'additional_ings.id as additional_ing_id', 'additional_ings.name as additional_ing_name', 'oadi.quantity as additional_ing_quantity', 'od.comment as dish_comment')
            ->join('orders_dishes as od', 'od.order_id', '=', 'orders.id')
            ->join('dishes', 'dishes.id', '=', 'od.dish_id')
            ->leftJoin('orders_dishes_additional_ings as oadi', function ($join) {
                $join->on('oadi.dish_id', '=', 'dishes.id')
                     ->on('oadi.order_id', '=', 'orders.id');
            })
            ->leftJoin('additional_ings', 'additional_ings.id', '=', 'oadi.additional_ing_id')
            ->where('orders.user_id', $customer_id)
            ->get();
        }

        $organizedOrders = [];

        foreach ($orders as $order) {
            $orderId = $order->order_id;
            $dishId = $order->dish_id;

            if (!isset($organizedOrders[$orderId])) {
                $organizedOrders[$orderId] = [
                    'order_id' => $orderId,
                    'order_status' => $order->order_status,
                    'order_date' => $order->order_date,
                    'order_price' => $order->order_price,
                    'location_id' => $order->location_id,
                    'user_id' => $order->customer_id,
                    'dishes' => []
                ];
            }
        
            if (!isset($organizedOrders[$orderId]['dishes'][$dishId])) {
                $organizedOrders[$orderId]['dishes'][$dishId] = [
                    'id' => $dishId,
                    'name' => $order->dish_name,
                    'image_path' => $order->dish_image,
                    'quantity' => $order->dish_quantity,
                    'comment' => $order->dish_comment,
                    'additional_ings' => []
                ];
            }
        
            if ($order->additional_ing_id) {
                $organizedOrders[$orderId]['dishes'][$dishId]['additional_ings'][] = [
                    'id' => $order->additional_ing_id,
                    'name' => $order->additional_ing_name,
                    'quantity' => $order->additional_ing_quantity
                ];
            }
        }
        
        foreach ($organizedOrders as &$order) {
            $order['dishes'] = array_values($order['dishes']);
        }
        $organizedOrders = array_values($organizedOrders);
        return response()->json([$organizedOrders]);
    }


    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array|min:1',
            'orders.*.cook_id' => 'required|exists:users,id|numeric',
            'orders.*.order_price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'orders.*.dishes' => 'required|array|min:1',
            'orders.*.dishes.*.dish_id' => 'required|exists:dishes,id|numeric',
            'orders.*.dishes.*.quantity' => 'required|numeric|gt:0',
            'orders.*.dishes.*.comment' => 'nullable|string|max:255',
            'orders.*.dishes.*.additional_ings' => 'sometimes|array',
            'orders.*.dishes.*.additional_ings.*.additional_ing_id' => 'sometimes|exists:additional_ings,id|numeric',
            'orders.*.dishes.*.additional_ings.*.quantity' => 'sometimes|numeric|gt:0',
            'user_id' => 'required|exists:users,id|numeric',
            'status' => 'required|string',
            'location_id' => 'required|exists:locations,id|numeric',
            'order_date' => 'required|date_format:Y-m-d H:i:s',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        foreach($request->orders as $order) {
            $newOrder = new Order();
            $newOrder->cook_id = $order['cook_id'];
            $newOrder->location_id = $request->location_id;
            $newOrder->status = $request->status;
            $newOrder->order_price = $order['order_price'];
            $newOrder->order_date = $request->order_date;
            $newOrder->user_id = $request->user_id;
            $newOrder->save();
            foreach ($order['dishes'] as $dish) {
                $newOrder->dishes()->attach($dish['dish_id'], ['quantity' => $dish['quantity'], 'comment' => $dish['comment']]);
                $dishModel = Dish::find($dish['dish_id']);
                if ($dishModel && isset($dish['additional_ings'])) {
                    foreach ($dish['additional_ings'] as $additional) {
                        OrderDishAdditional::create(['order_id' => $newOrder->id, 'dish_id' => $dishModel->id, 'additional_ing_id' => $additional['id'], 'quantity' => $additional['quantity']]);
                    }
                }
            }
        }

        if (!$order) {
            return response()->json(['message' => 'Error while creating order'], 401);
        }
        return response()->json(['message' => 'Order created successfully'], 201);
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



