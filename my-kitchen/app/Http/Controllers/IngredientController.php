<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use App\Http\Controllers\Additional_ingController;

class IngredientController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:ingredient-list');
         $this->middleware('permission:ingredient-create', ['only' => ['store']]);
         $this->middleware('permission:ingredient-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:ingredient-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $ingredientes= Ingredient::latest()->paginate(5);
        if (!$ingredientes) {
            return response()->json(['message' => 'No ingredients found'], 404);
        }
        return response()->json($ingredientes);
    }

    public function store(Request $request)
    {
        if ($request->isSelected == true) {
            $request->validate([
                'name' => 'required|string',
                'cost' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            ]);
            $ingredient = Ingredient::create($request->all());
            $additional_ing = Additional_ingController::store($request->user_id, $ingredient->id, $request->cost);
            if ($additional_ing['additional_ing'] == null) {
                return response()->json($additional_ing['message'], 400);
            }
        } else {
            $request->validate([
                'name' => 'required|string',
            ]);
            $ingredient = Ingredient::create($request->all());
        }
        return response()->json(['ingredient created successfully', 'ingredient' => $ingredient], 200);
    }

    public function show($id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }
        return response()->json($ingredient);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }
        $ingredient->update($request->all());
        return response()->json(['message' => 'Ingredient updated successfully', 'ingredient' => $ingredient]);
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }
        $ingredient->delete();
        return response()->json(['message' => 'Ingredient deleted successfully']);
    }
}
