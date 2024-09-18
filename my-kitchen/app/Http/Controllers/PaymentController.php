<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:payment-list');
         $this->middleware('permission:payment-create', ['only' => ['store']]);
    }

    public function index()
    {
        $payment= Payment::latest()->paginate(5);
        if ($payment['total'] == 0) {
            return response()->json(['message' => 'No payments found'], 404);
        }
        return response()->json($payment);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id|numeric',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'total_amount' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $payment = Payment::create($request->all());
        if (!$payment) {
            return response()->json(['message' => 'Error while processing payment'], 400);
        }
        return response()->json(['message' => 'Payment processed successfully', 'payment' => $payment], 201);
    }

    public function show($id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        return response()->json($payment);
    }
}
