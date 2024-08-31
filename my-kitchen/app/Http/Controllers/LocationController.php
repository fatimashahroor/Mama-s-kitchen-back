<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;

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
        if (!$location) {
            return response()->json(['message' => 'No location found'], 404);
        }
        return response()->json($location);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|numeric',
            'city' => 'required|string',
            'region' => 'required|string',
            'building' => 'required|string',
            'street' => 'required|string',
            'floor_nb' => 'required|numeric|between:0,50',
            'near' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        $location = Location::create($request->all());
        if (!$location) {
            return response()->json(['message' => 'Error while creating location'], 400);
        }
        return response()->json(['message' => 'Location created successfully', 'location' => $location], 201);
    }

    public function show($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        return response()->json(['location'=>$location], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'city' => 'required|string',
            'region' => 'required|string',
            'building' => 'required|string',
            'street' => 'required|string',
            'floor_nb' => 'required|numeric|between:0,50',
            'near' => 'required|string',
        ]);
        $location = Location::findOrFail($id);
        $location->update($request->all());
        return response()->json(['message' => 'Location updated successfully', 'location' => $location], 200);
    }

    public function destroy($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        $location->delete();
        return response()->json(['message' => 'Location deleted successfully'], 200);
    }
}
