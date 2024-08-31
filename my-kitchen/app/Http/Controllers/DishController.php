<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
class DishController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:dish-list', ['only'=>['show']]);
         $this->middleware('permission:dish-create', ['only' => ['create','store']]);
         $this->middleware('permission:dish-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:dish-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $dishes = Dish::latest()->paginate(5);
        return response()->json($dishes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'steps' => 'sometimes|text',
        ]);

        $dish=Dish::create($request->all());
        $this->uploadPhoto($request, $dish->id);
        return response()->json(['message' => 'Dish created successfully', 'dish' => $dish], 201);
    }

    public function uploadPhoto(Request $request, $id)
    {
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,jpg|max:2048',
    ]);
    $dish = Dish::find($id);
    $this->deletePhoto($dish);
    $imageName = time().'.'.$request->photo->extension();  
    $request->photo->move(public_path('images'), $imageName);
    $dish->image_path = $imageName;
    $dish->save();

    return back()
        ->with('success','You have successfully uploaded an image.');
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

    public function update(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);
        if (!$dish) {
            return response()->json(['message' => 'Dish not found'], 404);
        }
        // $request->validate([
        //     'name' => 'required|string',
        //     'price' => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
        //     'steps' => 'sometimes|nullable|string',
        // ]);
        
        $dish->name = $request->name;
        $dish->price = $request->price;
        $dish->steps = $request->steps;

        $dish->save();
        $this->uploadPhoto($request, $id); 
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
