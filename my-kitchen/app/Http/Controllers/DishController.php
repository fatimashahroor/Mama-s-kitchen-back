<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
class DishController extends Controller
{
    function __construct()
    {
         $this->middleware(middleware: 'permission:dish-list', options: ['only'=>['show']]);
         $this->middleware(middleware: 'permission:dish-create', options: ['only' => ['store']]);
         $this->middleware(middleware: 'permission:dish-edit', options: ['only' => ['update']]);
         $this->middleware(middleware: 'permission:dish-delete', options: ['only' => ['destroy']]);
    }

    public function index()
    {
        $dish= Dish::with(['user' => function($query) {
            $query->select('id', 'full_name');}])->latest()->paginate(20);
        if ($dish['total'] === 0) {
            return response()->json(['message' => 'No dishes found'], 404);
        }
        $dish->getCollection()->transform(function ($dish) {
            if ($dish->user) {
                $dish->user_full_name = $dish->user->full_name ?? 'No user found';
            }
            return $dish;
        });
        return response()->json($dish);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'steps' => 'required|string',
            'available_on' => 'required|string',
            'diet_type' => 'required|string',
            'duration' => 'required|date_format:H:i:s',
            'main_ingredients' => 'required|string',
            'photo' => 'sometimes|image|mimes:jpeg,jpg,png',
            'additional_ings' => 'sometimes',
        ]);
        if ($validator->fails()) {
            $response = response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
            return $response;
        }
        $dish = new Dish();
        $dish->user_id = $request->user_id;
        $dish->name = $request->name;
        $dish->price = $request->price;
        $dish->steps = $request->steps;
        $dish->available_on = $request->available_on;
        $dish->diet_type = $request->diet_type;
        $dish->duration = $request->duration;
        $dish->main_ingredients = $request->main_ingredients;
        $dish->image_path = $this->uploadPhoto($dish, $request->photo);
        $dish->save();
        
        $additional_ings = json_decode($request->additional_ings);
        $additional_ings = explode(',',$additional_ings);
        $additional_ings = array_map('intval', $additional_ings);
        
        for ($i=0; $i<count($additional_ings); $i++) 
            $dish->additional_ingredients()->attach($additional_ings[$i]);
        return response()->json(['message' => 'Dish created successfully', 'dish' => $dish], 201);
    }

    private function uploadPhoto($dish, $photo)
    {
        $this->deletePhoto($dish);
        $imageName = time().'.'.$photo->extension();  
        $photo->move(public_path('images'), $imageName);
        return $imageName;
    }

    private function deletePhoto($dish){
        $oldImage = $dish->image_path;
        if ($oldImage && file_exists(public_path("images/{$oldImage}"))) {
            unlink(public_path("images/{$oldImage}"));
        }
    }
    public function show($id)
    {
        $dish = Dish::find($id);
        if (!$dish) {
            return response()->json(['message' => 'Dish not found'], 404);
        }
        return response()->json($dish);
    }
    public function getDishIngredients($id)
    {
        $dish = Dish::where('id', $id)->with('additional_ingredients')->first();    
        if (!$dish) {
            return response()->json(['message' => 'Dish not found'], 404);
        }
        if ($dish->additional_ingredients->isEmpty()) {
            return response()->json(['message' => 'No additional ingredients found'], 404);
        }
        return response()->json($dish->additional_ingredients, 200);
    }
    public function getDishesByUser($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $dishes= Dish::where('user_id', $user_id)->get();
        if ($dishes->isEmpty()) {
            return response()->json(['message' => 'No dishes found'], 404);
        }
        return response()->json($dishes);
    }

    public function update(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);
        if (!$dish) {
            return response()->json(['message' => 'Dish not found'], 404);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'steps' => 'required|string',
            'available_on' => 'required|string',
            'diet_type' => 'required|string',
            'duration' => 'required|date_format:H:i:s',
            'main_ingredients' => 'required|string',
            'photo' => 'sometimes|image|mimes:jpeg,jpg,png',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $dish->name = $request->name;
        $dish->price = $request->price;
        $dish->steps = $request->steps;
        $dish->available_on = $request->available_on;
        $dish->diet_type = $request->diet_type;
        $dish->duration = $request->duration;
        $dish->main_ingredients = $request->main_ingredients;
        if($request->photo)
            $dish->image_path = $this->uploadPhoto($dish, $request->photo);
        $dish->save();
        return response()->json(['message' => 'Dish updated successfully', 'dish' => $dish]);   
    }

    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);
        $this->deletePhoto($dish);
        $dish->delete();
        return response()->json(['message' => 'Dish deleted successfully']);
    }
}
