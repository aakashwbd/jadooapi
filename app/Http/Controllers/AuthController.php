<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth:sanctum'], ['only' => ['logout', 'me']]);
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'validate_error',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            if (!auth()->attempt($validator->validated())) {
                return response([
                    'status' => 'error',
                    'message' => "Credentials doesn't matched..."
                ], 401);
            }

            $accessToken = auth()->user()->createToken('authToken');
            return response([
                'status' => 'done',
                'message' => 'Successfully logged in...',
                'data' => [
                    'token' => 'Bearer ' . $accessToken->plainTextToken,
                    'user' => auth()->user()
                ]
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => 'serverError',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function register($request)

    {
        dd($request->all());
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
//            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'validate_error',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
            ]);


            return response([
                'status' => 'done',
                'message' => 'Successfully registered...'
            ], 201);

            
        } catch (Exception $e) {
            return response([
                'status' => 'serverError',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response([
                'status' => 'done',
                'message' => 'Successfully logout...',
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => 'serverError',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function me()
    {
        try {
            return response([
                'status' => 'done',
                'data' => auth()->user()
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => 'serverError',
                'message' => $e->getMessage()
            ]);
        }
    }
}
