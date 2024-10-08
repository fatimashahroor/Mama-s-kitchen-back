<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:location-list', ['only' => ['index']]);
         $this->middleware('permission:location-create', ['only' => ['store']]);
         $this->middleware('permission:location-edit', ['only' => ['update']]);
         $this->middleware('permission:location-delete', ['only' => ['destroy']]);
    }

    public function index($user_id)
    {
        $location = Location::where('user_id', $user_id)->get();
        if ($location->isEmpty()) {
            return response()->json(['message' => 'No locations found'], 404);
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
        $validator = Validator::make($request->all(), [
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
