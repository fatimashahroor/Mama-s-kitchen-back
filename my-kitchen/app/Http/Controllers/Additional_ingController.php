<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Additional_ing;
use Illuminate\Support\Facades\Validator;

class Additional_ingController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:additional_ing-list');
         $this->middleware('permission:additional_ing-edit', ['only' => ['update']]);
         $this->middleware('permission:additional_ing-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $additional_ings= Additional_ing::latest()->paginate(5);
        if ($additional_ings['total'] == 0) {
            return response()->json(['message' => 'No additional ingredients found'], 404);
        }
        return response()->json($additional_ings);
    }

    public static function store($user_id, $ingredient_id, $cost)
    {
        $additional_ing = new Additional_ing();
        $additional_ing->user_id = $user_id;
        $additional_ing->ingredient_id = $ingredient_id;
        $additional_ing->cost = $cost;
        if (!$additional_ing->save()) {
            return ['message' => 'Error while creating additional ingredient'];
        }
        return ['message' => 'Additional ingredient created successfully', 'additional_ing' => $additional_ing];
    }

    public function update ($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cost' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $additional_ing = Additional_ing::find($id);
        if (!$additional_ing) {
            return response()->json(['message' => 'Additional ingredient not found'], 404);
        }
        $additional_ing->update($request->all());
        return response()->json(['message' => 'Additional ingredient updated successfully', 'additional_ing' => $additional_ing], 200);
    }
    public function show($id)
    {
        $additional_ing= Additional_ing::find($id);
        if (!$additional_ing) {
            return response()->json(['message' => 'Additional ingredient not found'], 404);
        }
        return response()->json($additional_ing);
    }

    public function destroy($id)
    {
        $additional_ing = Additional_ing::find($id);
        if (!$additional_ing) {
            return response()->json(['message' => 'Additional ingredient not found'], 404);
        }
        $additional_ing->delete();
        return response()->json(['message' => 'Additional ingredient deleted successfully']);
    }
}
