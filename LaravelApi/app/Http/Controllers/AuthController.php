<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dashboard;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'password' => 'required|string|min:6|confirmed',
                ]
            );

            if ($validatedData->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validatedData->errors()
                ]);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            // $token = auth('api')->login($user);
            // return $this->respondWithToken($token);

            // echo"Register Api";

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                //     'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                // 'errors' => $validatedData->errors()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $validatedData = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if ($validatedData->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validatedData->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & password do not match with our records',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'user_id' => $user->id, // Return the user ID
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function postDashboardData(Request $request)
{
    $user = auth()->user();
    
    try {
        // Validate the request data
        $validatedData = Validator::make(
            $request->all(),
            [
                'State' => 'required|string',
                'Country' => 'required|string',
                'City' => 'required|string',
                'Price' => 'required|integer',
                'Duration' => 'required|integer',
                'Date' => 'required|date',
                'Traveltype' => 'required|string'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validatedData->errors()
            ], 400);
        }

        // Add user_id to the validated data
        $inputData = $validatedData->validated();
        $inputData['user_id'] = $user->id;

        // Process the data (e.g., save it to the database)
        $dashboardData = Dashboard::create($inputData);

        return response()->json([
            'status' => true,
            'message' => 'Data saved successfully',
            // 'data' => $dashboardData,
        ], 201);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}

    // public function postDashboardData(Request $request)
    // {
    //     $user = auth()->user();
    //     // echo ">>>>>" . $user;
    //     try {
    //         // Validate the request data
    //         $validatedData = Validator::make(
    //             $request->all(),
    //             [
    //                 'user_id' => $user->id, // Ensure user_id is provided and valid
    //                 'State' => 'required|string',
    //                 'Country' => 'required|string',
    //                 'City' => 'required|string',
    //                 'Price' => 'required|integer',
    //                 'Duration' => 'required|integer',
    //                 'Date' => 'required|date',
    //                 'Traveltype' => 'required|string'
    //             ]
    //         );

    //         $inputData = array(
    //             'user_id' => $user->id,
    //             'State' => $request->State,
    //             'Country' => $request->Country,
    //             'City' => $request->City,
    //             'Price' => $request->Price,
    //             'Duration' => $request->Duration,
    //             'Date' => $request->Date,
    //             'Traveltype' => $request->Traveltype
    //         );

    //         // Process the data (e.g., save it to the database)
    //         $dashboardData = Dashboard::create($inputData);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data posted successfully',
    //             'data' => $dashboardData,
    //         ], 201);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage(),
    //         ], 500);
    //     }
    // }

    // Method to handle GET requests to the dashboard
    public function getDashboardData()
    {
        // Retrieve the data (e.g., from the database)
        $dashboardData = Dashboard::where('user_id', auth()->id())->get();

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $dashboardData,
        ], 200);
    }

    public function logout(Request $request)
    {
        // Retrieve the authenticated user and delete all tokens
        $user = auth()->user();
        if ($user) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not authenticated',
            'data' => []
        ], 401);
    }
}   
    
    // public function login()
    // {
    //     $credentials = request(['email', 'password']);

    //     if (!$token = auth()->attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     return $this->respondWithToken($token);
    // }


    // public function me()
    // {
    //     return response()->json(auth()->user());
    // }


    // public function logout()
    // {
    //     auth()->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }


    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }


    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth('api')->factory()->getTTL() * 60
    //     ]);
    // }
