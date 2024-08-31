<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

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
        return response()->json($ingredientes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $ingredient = Ingredient::create($request->all());
        return response()->json(['ingredient created successfully', 'ingredient' => $ingredient], 200);
    }
}
