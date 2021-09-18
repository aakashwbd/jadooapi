<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Profile;


class AuthController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth:sanctum'], ['only' => ['logout', 'me', 'update']]);
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
                    'user' => auth()->user()->load(["profile"])

                ]
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => 'serverError',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function register()

    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',

        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'validate_error',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'username' => request('username'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
            ]);

            if ($user) {
                $profile = Profile::create([
                    "user_id" => $user->id 
                    
                ]);
            }

        
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
    public function update(){
        try{
            $userId = Auth::id();
            
            $profile = Profile::where('user_id', $userId)->first();
            
            $profile->name = request('name') ?? $profile->name;
            $profile->dob = request('dob') ?? $profile->dob;
            $profile->bloodGroup = request('bloodGroup') ?? $profile->bloodGroup;
            $profile->maritialStatus = request('maritialStatus') ?? $profile->maritialStatus;
            $profile->nationality = request('nationality') ?? $profile->nationality;
            $profile->nid = request('nid') ?? $profile->nid;
            $profile->birthCertificate = request('birthCertificate') ?? $profile->birthCertificate;
            $profile->passport = request('passport') ?? $profile->passport;
            $profile->aboutDescripton = request('aboutDescripton') ?? $profile->aboutDescripton;
            $profile->summaryDescripton = request('summaryDescripton') ?? $profile->summaryDescripton;
            $profile->addressLabel = request('addressLabel') ?? $profile->addressLabel;
            $profile->streetAddress = request('streetAddress') ?? $profile->streetAddress;
            $profile->division = request('division') ?? $profile->division;
            $profile->district = request('district') ?? $profile->district;
            // $profile->summaryDescripton = request('summaryDescripton') ?? $profile->summaryDescripton;
            
            $profile->update();
            
            
            return response([
                'status' => 'done',
                'message' => 'Profile updated successfully',
                'data' => auth()->user()->load('profile')
            ]);
        }catch(Exception $e){
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
                'data' => auth()->user()->load('profile')
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => 'serverError',
                'message' => $e->getMessage()
            ]);
        }
    }
}
