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
    }

    public function index()
    {
        $payment= Payment::latest()->paginate(5);
        if ($payment['total'] == 0) {
            return response()->json(['message' => 'No payments found'], 404);
        }
        return response()->json($payment);
    }
}
