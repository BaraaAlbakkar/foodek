<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'string|required|max:255',
            'email' => 'string|required|unique:users|email',
            'password' => [
                'string',
                'required',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/',
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->subYears(16)->toDateString(),
            ],
            'phone_number' => 'required|string|unique:users|regex:/^07[56789]\d{7}$/',
        ]);

        if($validator->fails()){
            return $this->api_response(false,'Validation Error',['error' => $validator->errors()],422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'phone_number' => $request->phone_number
        ]);

        $defaultRole = Role::where('name_en','client')->first();
        if($defaultRole){
            $user->roles()->syncWithoutDetaching([$defaultRole->id]);
        }

        $data =[
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ];

        return $this->api_response(true,'Your Account has been Created',$data,201);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return $this->api_response(false,'Validator Error',['error' => $validator->errors()],422);
        }

        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password , $user->password)){

            return $this->api_response(false,'Incorrect Email or Password',[],401);
        }

        $user_name = User::where('email',$request->email)->first();
        $data = [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ];

        return $this->api_response(true, 'Welcome ' . $user_name->name, $data);
    }

    public function logout(Request $request){

        $request->user()->tokens()->delete();
        return $this->api_response(true,'logged out successfully');
    }
}
