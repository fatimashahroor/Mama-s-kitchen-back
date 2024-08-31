<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Additional_ing;
use Illuminate\Support\Facades\Validator;

class Additional_ingController extends Controller
{
    function __construct()
    {
        //  $this->middleware('permission:additional_ing-list');
        //  $this->middleware('permission:additional_ing-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:additional_ing-delete', ['only' => ['destroy']]);
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
}
