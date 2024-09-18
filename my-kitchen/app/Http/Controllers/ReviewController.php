<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|numeric',
            'dish_id' => 'required|exists:dishes,id|numeric',
            'rating' => 'required|numeric|between:0,5',
            'comment' => 'required|string',
        ]);
        $existingReview = Review::where('user_id', $request->user_id)->where('dish_id', $request->dish_id)->first();
        if ($existingReview) {
            return response()->json([
                'message' => 'You have already submitted a review on this dish.'], 409);
        }
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Rating or review section is empty',
                'errors' => $validator->errors()
            ], 400);
        }
        $review = Review::create($request->all());
        $review->save();
        return response()->json(['message' => 'Review created successfully', 'review' => $review], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:0,5',
            'comment' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()], 400);
        }
        $review = Review::findOrFail($id);
        $review->update($request->all());
        return response()->json(['message' => 'Review updated successfully', 'review' => $review], 200);
    }

    public function getDishReviews($dish_id)
    {
        $reviews = Review::with('user')->where('dish_id', $dish_id)->latest()->paginate(10);
        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No Reviews found'], 404);
        }
        $reviewsWithUser = $reviews->map(function ($review) {
            return [
                'id' => $review->id, 'user_id' => $review->user_id, 'full_name' => $review->user->full_name,
                'dish_id' => $review->dish_id, 'rating' => $review->rating, 'comment' => $review->comment,
                'created_at' => $review->created_at,'updated_at' => $review->updated_at,
            ];
        });
    
        return response()->json($reviewsWithUser);
    }
    
    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully'], 200);
    }
}
