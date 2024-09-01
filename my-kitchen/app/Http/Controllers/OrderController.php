<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:order-list');
         $this->middleware('permission:order-create', ['only' => ['store']]);
         $this->middleware('permission:order-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $orders= order::latest()->paginate(5);
        if (!$orders) {
            return response()->json(['message' => 'No orders found'], 404);
        }
        return response()->json($orders);
    }
}
