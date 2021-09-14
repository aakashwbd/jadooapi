<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BasicInfo;
use Validator;
class BasicInfoController extends Controller
{
    public function store (){
        try{
            $validate = Validator::make(request()->all(),[
            'name'    => 'required|min:5',
            'dob'    => 'required',
            'bloodGroup' => 'required',
            'maritialStatus' => 'required',
            'nationality' => 'required',
            'nid' => 'required',
            'birthCertificate' => 'required',

            ]);
            
            if($validate->fails()){
                return response([
                    'status' => 'validation_error',
                    'message' => $validate->errors()
                ]);
            }
         
           $basicInfo = BasicInfo::create([
           "name" => request('name'),
           "dob" => request('dob'),
           "bloodGroup" => request('bloodGroup'),
           "maritialStatus" => request('maritialStatus'),
           "nationality" => request('nationality'),
           "nid" => request('nid'),
           "birthCertificate" => request('birthCertificate'),
     
            ]);
            if($basicInfo){
                return response([
                    'status' => 'success',
                    'message' => $basicInfo
                ]);
            }
            
        //    if($user){
        //         $userprofile = UserProfile::create([
        //             'user_id'=> $user->_id,
        //             'name'   => request('fname'),
        //             'phone'  => request('phone'),
        //         ]);
        //         if($userprofile){
        //             return response([
        //                 'status' => 'success',
        //                 'message' => 'Registration Successfully Create'
        //             ]);
        //         }
        //    }
        }catch(\Exception $e){
            return response([
                'status' => 'server_error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show(){
        $allBasicInfo = BasicInfo::all()->get();
        return response([
            'status' => 'success',
            'data' =>  $allBasicInfo
        ]);
    }
}
