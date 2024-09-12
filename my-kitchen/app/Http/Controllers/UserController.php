<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    function __construct()
    {
        $this->middleware(middleware: 'permission:user-list');
        $this->middleware(middleware: 'permission:user-create', options: ['only' => ['store']]);
        $this->middleware(middleware: 'permission:user-edit', options: ['only'=> ['update']]);
        $this->middleware(middleware: 'permission:user-delete', options: ['only' => ['destroy']]);
        $this->middleware(middleware: 'permission:rating-create', options: ['only' => ['setRating']]);
        $this->middleware(middleware: 'permission:rating-list', options: ['only' => ['getOverallRating']]);
        $this->middleware(middleware: 'permission:cooks-list', options: ['only' => ['listCooks']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with(relations: 'roles')->orderBy(column: 'id',direction: 'ASC')->paginate( 5);
        $users->getCollection()->transform(callback: function ( $user): array {
            return [
                'user' => $user,
                'role' => $user->getRoleNames()
            ];
        });
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(rules: [
            'full_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required'
        ]);
        
        $input = $request->all();
        $input['password'] = Hash::make(value: $input['password']);

        $user = User::create($input);
        $user->assignRole($request->input(key: 'roles'));

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function setRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|numeric',
            'rating' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->rating = $user->rating + $request->rating;
        $user->rating_count = $user->rating_count + 1;
        $user->save();
        $overall_rating = round($user->rating / $user->rating_count);
        return response()->json(['message' => 'Rating updated successfully', 'overall_rating'=>$overall_rating],200);
    }

    public function getOverallRating($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->rating_count == 0) {
            return response()->json(['overall_rating' => 0]);
        }
        $averageRating = round($user->rating / $user->rating_count);
        return response()->json(['overall_rating' => $averageRating]);
    }

    
    public function listCooks()
    {
        $users = User::whereHas('roles',function($query) {$query->where('name', 'cook');  
        })->get();
        if (!$users) {
            return response()->json(['message' => 'No cooks found'], 404);
        }
        $cooks = $users->map(function ($user) {
            return ['id' => $user->id,'full_name' => $user->full_name,'age' => $user->age,'phone' => $user->phone,'bio' => $user->bio,
                'located_in' => $user->located_in,'image_path' => $user->image_path,'rating' => $user->rating,'status' => $user->status,
                'rating_count' => $user->rating_count];
        });
    
        return response()->json(['cooks' => $cooks], 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $role = Role::find($user->roles[0]->id);
        $role->permissions;

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['user'=>['id'=>$user->id, 'full_name'=>$user->full_name, 'email'=>$user->email, 'age'=>$user->age,
        'phone'=>$user->phone, 'bio'=>$user->bio, 'located_in'=>$user->located_in, 'image_path'=>$user->image_path, 
        'overall_rating'=>$user->rating/$user->rating_count,'status'=>$user->status, 'created_at'=>$user->created_at, 'updated_at'=>$user->updated_at], 
        'role'=>$role]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'age' => 'required|numeric',
            'bio' => 'required|string',
            'located_in' => 'required|string',
            'status' => 'required|string',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->phone = $request->phone;
        $user->age = $request->age;
        $user->bio = $request->bio;
        $user->located_in = $request->located_in;
        $user->status = $request->status;
        if($request->photo)
            $user->image_path = $this->uploadPhoto($user, $request->photo);
        $user->save();
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }


    private function uploadPhoto($user, $photo)
    {
        $this->deletePhoto($user);
        $imageName = time().'.'.$photo->extension();  
        $photo->move(public_path('images'), $imageName);
        return $imageName;
    }

    private function deletePhoto($user){
        $oldImage = $user->image_path;
        if ($oldImage && file_exists(public_path("images/{$oldImage}"))) {
            unlink(public_path("images/{$oldImage}"));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

}