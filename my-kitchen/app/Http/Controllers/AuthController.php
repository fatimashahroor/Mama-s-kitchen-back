<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users', 
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole([3]);
        $token = JWTAuth::fromUser($user);
        return response()->json(['message' => 'User created successfully', 'user' => $user, 
        'token' => $token], 201);
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $credentials = request(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid user credentials'], 401);
        }
        $user = Auth::user(); 

        return $this->respondWithToken($token, $user);
    
    }
    /**
     * 
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user)
    {
       
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id, 
                'roles' => $user->roles()->pluck('id'),
                'permissions'=> $user->roles->flatMap(function ($role) {
                    return $role->permissions->pluck('id');})->unique()
            ]
        ]);
    }

    public function refreshToken(Request $request) {
        try {
            $newToken = auth('api')->refresh(); 
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Refresh token is invalid'], 401);
        }

        return response()->json(['token' => $newToken]);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function logout(Request $request)
     {
         try {
             // Invalidate the token
             JWTAuth::invalidate(JWTAuth::getToken());
     
             return response()->json([
                 'status' => 'success',
                 'message' => 'Successfully logged out',
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Failed to log out',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

}
