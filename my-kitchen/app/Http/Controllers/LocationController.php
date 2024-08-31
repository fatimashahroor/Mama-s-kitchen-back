<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:location-list');
         $this->middleware('permission:location-create', ['only' => ['create','store']]);
         $this->middleware('permission:location-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:location-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $location = Location::latest()->paginate(5);
        return response()->json($location);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|numeric',
            'city' => 'required|string',
            'region' => 'required|string',
            'building' => 'required|string',
            'street' => 'required|string',
            'floor_nb' => 'required|numeric',
            'near' => 'required|string',
        ]);
        $location = Location::create($request->all());
        return response()->json(['message' => 'Location created successfully', 'location' => $location], 201);
    }
}
