<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $req)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users', // duy nhat
            'password' => 'required|string|min:8',
            // 'repassword' => 'required|string|same:password'
        ];
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        //create new user
        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password)
        ]);
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $response = ['user' => $user,'token' => $token];
        return response()->json($response,200);
    }
    public function login(Request $req)
    {
        $rules =[
            'email' => 'required',
            'password' => 'required|string'
        ];
        $req->validate($rules);
        $user = User::where('email',$req->email)->first(); // find email
        // found email and pass correct
        if($user && Hash::check($req->password, $user->password))
        {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response=['user'=>$user,'token'=>$token];
            return response()->json($response,200);
        }
        $response = ['message' => 'Incorrect email or passwords'];
        return response()->json($response,400);
    }
}
