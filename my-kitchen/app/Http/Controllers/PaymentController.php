<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:payment-list');
         $this->middleware('permission:payment-create', ['only' => ['store']]);
         $this->middleware('permission:payment-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:payment-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $payment= Payment::latest()->paginate(5);
        if (!$payment) {
            return response()->json(['message' => 'No payments found'], 404);
        }
        return response()->json($payment);
    }
}
