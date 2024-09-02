<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:review-list');
         $this->middleware('permission:review-create', ['only' => ['store']]);
         $this->middleware('permission:review-edit', ['only' => ['update']]);
         $this->middleware('permission:review-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $reviews = Review::latest()->paginate(5);
        if ($reviews['total'] == 0) {
            return response()->json(['message' => 'No reviews found'], 404);
        }
        return response()->json($reviews);
    }
}
