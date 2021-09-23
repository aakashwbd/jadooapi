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

            $profile->firstName = request('firstName') ?? $profile->firstName;
            $profile->lastName = request('lastName') ?? $profile->lastName;
            $profile->summary = request('summary') ?? $profile->summary;
            $profile->githubUserName = request('githubUserName') ?? $profile->githubUserName;
            $profile->linkedinUserName = request('linkedinUserName') ?? $profile->linkedinUserName;
            $profile->countries = request('countries') ?? $profile->countries;
            // $profile->phones = request('phones') ?? $profile->phones;
            $profile->role = request('role') ?? $profile->role;
            $profile->salary = request('salary') ?? $profile->salary;
            // $profile->degree = request('degree') ?? $profile->degree;
            // $profile->institute = request('institute') ?? $profile->institute;
            // $profile->startDate = request('startDate') ?? $profile->startDate;
            // $profile->endDate = request('endDate') ?? $profile->endDate;
            // $profile->educationLabel = request('educationLabel') ?? $profile->educationLabel;
            $profile->educations = request('educations') ?? $profile->educations;
            $profile->experiences = request('experiences') ?? $profile->experiences;
            $profile->trainings = request('trainings') ?? $profile->trainings;
            $profile->fullTimeJob = request('fullTimeJob') ?? $profile->fullTimeJob;
            $profile->skills = request('skills') ?? $profile->skills;
            $profile->phone = request('phone') ?? $profile->phone;


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
